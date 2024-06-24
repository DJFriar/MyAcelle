<?php

namespace Acelle\Model;

use Illuminate\Database\Eloquent\Model;
use Exception;
use Acelle\Model\Invoice;
use Acelle\Model\InvoiceItem;
use Acelle\Library\Traits\HasUid;
use Carbon\Carbon;
use Acelle\Library\Facades\SubscriptionFacade;
use Acelle\Library\CreditTracker;
use Acelle\Library\RateTracker;
use Acelle\Library\RateLimit;
use Illuminate\Database\Eloquent\Builder;

class Subscription extends Model
{
    use HasUid;

    public const STATUS_NEW = 'new';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_ENDED = 'ended';           //
    public const STATUS_CANCELLED = 'cancelled';   // Customer chooses "End now"
    public const STATUS_TERMINATED = 'terminated'; // Admin terminates a subscription

    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'cancelled_at', 'current_period_ends_at',
        'created_at', 'updated_at', 'terminated_at'
    ];

    /**
     * Indicates if the plan change should be prorated.
     *
     * @var bool
     */
    protected $prorate = true;

    /**
     * Associations.
     *
     * @var object | collect
     */
    public function plan()
    {
        // @todo dependency injection
        return $this->belongsTo('\Acelle\Model\Plan');
    }

    public function planGeneral()
    {
        // @todo dependency injection
        return $this->belongsTo('\Acelle\Model\PlanGeneral', 'plan_id')
            ->where('type', \Acelle\Model\PlanGeneral::TYPE_GENERAL);
    }

    public function getPlanName()
    {
        return $this->plan->name;
    }

    public function scopeNew($query)
    {
        return $query->whereIn('status', [ self::STATUS_NEW ]);
    }

    public function scopeNewOrActive($query)
    {
        return $query->whereIn('status', [ self::STATUS_ACTIVE, self::STATUS_NEW ]);
    }

    /**
     * Get the user that owns the subscription.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer()
    {
        // @todo dependency injection
        return $this->belongsTo('\Acelle\Model\Customer');
    }

    public function getCustomerName()
    {
        return $this->customer->displayName();
    }

    /**
     * Get related invoices.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function invoices()
    {
        $id = $this->id;
        $type = self::class;
        return Invoice::whereIn('id', function ($query) use ($id, $type) {
            $query->select('invoice_id')
            ->from(with(new InvoiceItem())->getTable())
            ->where('item_type', $type)
            ->where('item_id', $id);
        });
    }

    /**
     * Subscription only has one new invoice.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function getUnpaidInvoice()
    {
        $invoice = $this->invoices()
            ->unpaid()
            ->orderBy('created_at', 'desc')
            ->first();

        return $invoice ? $invoice->mapType() : $invoice;
    }

    public function scopeActive($query)
    {
        $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeEnded($query)
    {
        $query->where('status', self::STATUS_ENDED);
    }

    public function scopeCancelled($query)
    {
        $query->where('status', self::STATUS_CANCELLED);
    }

    public function scopeCancelledOrEdned($query)
    {
        $query->whereIn('status', [self::STATUS_CANCELLED, self::STATUS_ENDED]);
    }

    /**
     * Get last invoice.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function getItsOnlyUnpaidInitInvoice()
    {
        if (!$this->isNew()) {
            throw new \Exception('Method getItsOnlyUnpaidInitInvoice() only use for NEW subscription');
        }

        $query = $this->invoices()
            ->newSubscription()
            ->unpaid();

        // new sub luôn phải có duy nhất 1 invoice
        if ($query->count() > 1) {
            throw new \Exception('New Subscription must have only one unpaid TYPE_NEW_SUBSCRIPTION invoice!');
        }

        return $query->first()->mapType();
    }

    /**
     * Get renew invoice.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function getItsOnlyUnpaidChangePlanInvoice()
    {
        if (!$this->isActive()) {
            throw new \Exception('Method getItsOnlyUnpaidChangePlanInvoice() only use for ACTIVE subscription!');
        }

        $query = $this->invoices()
            ->changePlan()
            ->unpaid();

        if ($query->count() > 1) {
            throw new \Exception('Somehow sub has more than one unpaid change plan invoice!');
        }

        return $query->first() ? $query->first()->mapType() : $query->first();
    }

    /**
     * Create init invoice.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createNewSubscriptionInvoiceWithoutTrial()
    {
        $endDate = $this->customer->formatDateTime($this->getPeriodEndsAt(Carbon::now()), "datetime_full");

        // create init invoice
        $invoice = \Acelle\Model\InvoiceNewSubscription::createInvoice(
            $type = \Acelle\Model\InvoiceNewSubscription::TYPE_NEW_SUBSCRIPTION,
            $title = trans('messages.invoice.init_subscription'),
            $description = trans('messages.invoice.init_subscription.desc', [
                'plan' => $this->plan->name,
                'date' => $endDate,
            ]),
            $customer_id = $this->customer->id,
            $currency_id = $this->plan->currency_id,
            $billing_address = $this->customer->getDefaultBillingAddress(),
            $invoiceItems = [
                new InvoiceItem([
                    'item_id' => $this->id,
                    'item_type' => get_class($this),
                    'amount' => $this->plan->getPrice(),
                    'title' => $this->plan->name,
                    'description' => view('plans._bill_desc', ['plan' => $this->plan]),
                ]),
            ],
        );

        // save subscription to invoice
        $invoice->subscription_id = $this->id;
        $invoice->save();

        // return
        return $invoice;
    }

    /**
     * Create trial invoice.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createNewSubscriptionInvoiceWithTrial()
    {
        $endDate = $this->customer->formatDateTime($this->getTrialPeriodEndsAt(Carbon::now()), "datetime_full");

        // create init invoice
        $invoice = \Acelle\Model\InvoiceNewSubscription::createInvoice(
            $type = \Acelle\Model\InvoiceNewSubscription::TYPE_NEW_SUBSCRIPTION,
            $title = trans('messages.invoice.init_subscription'),
            $description = trans('messages.invoice.init_subscription_with_trial.desc', [
                'plan' => $this->planGeneral->name,
                'date' => $endDate,
                'amount' => $this->planGeneral->getFormattedPrice(),
                'period' => $this->planGeneral->displayFrequencyTime(),
            ]),
            $customer_id = $this->customer->id,
            $currency_id = $this->planGeneral->currency_id,
            $billing_address = $this->customer->getDefaultBillingAddress(),
            $invoiceItems = [
                new InvoiceItem([
                    'item_id' => $this->id,
                    'item_type' => get_class($this),
                    'amount' => 0, // trial => amount = 0
                    'title' => $this->planGeneral->name,
                    'description' => view('plans._bill_desc', ['plan' => $this->planGeneral]),
                ]),
            ],
        );

        // save subscription to invoice
        $invoice->subscription_id = $this->id;
        $invoice->save();

        // return
        return $invoice;
    }

    /**
     * Create renew invoice.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createRenewInvoice()
    {
        //
        if ($this->getItsOnlyUnpaidRenewInvoice()) {
            throw new \Exception("Hey, đang có 1 unpaid renew invoice khác. Không thể tạo renew invoice nữa! " . $this->getUnpaidInvoice()->toJson());
        }

        if ($this->status != self::STATUS_ACTIVE) {
            throw new \Exception("Hey, subscription này đâu có active đâu mà đòi renew???");
        }

        // create init invoice
        $invoice = \Acelle\Model\InvoiceRenewSubscription::createInvoice(
            $type = \Acelle\Model\InvoiceRenewSubscription::TYPE_RENEW_SUBSCRIPTION,
            $title = trans('messages.invoice.renew_subscription'),
            $description = trans('messages.renew_subscription.desc', [
                'plan' => $this->plan->name,
                'date' => $this->customer->formatDateTime($this->nextPeriod(), 'datetime_full'),
            ]),
            $customer_id = $this->customer->id,
            $currency_id = $this->plan->currency_id,
            $billing_address = $this->customer->getDefaultBillingAddress(),
            $invoiceItems = [
                new InvoiceItem([
                    'item_id' => $this->id,
                    'item_type' => get_class($this),
                    'amount' => $this->plan->getPrice(),
                    'title' => $this->plan->name,
                    'description' => view('plans._bill_desc', ['plan' => $this->plan]),
                ]),
            ],
        );

        // save subscription to invoice
        $invoice->subscription_id = $this->id;
        $invoice->save();

        // return
        return $invoice;
    }

    /**
     * Create change plan invoice.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createChangePlanInvoice($newPlan)
    {
        if ($this->status != self::STATUS_ACTIVE) {
            throw new \Exception("Hey, subscription này đâu có active đâu mà đòi change plan???");
        }

        // already has change plan invoice
        if ($this->getItsOnlyUnpaidChangePlanInvoice()) {
            throw new \Exception("Hey, subscription này đã có unpaid change plan invoice rồi???");
        }

        // calculate change plan amout ends at
        $metadata = $this->calcChangePlan($newPlan);

        // create init invoice
        $invoice = \Acelle\Model\InvoiceChangePlan::createInvoice(
            $type = \Acelle\Model\InvoiceChangePlan::TYPE_CHANGE_PLAN,
            $title = trans('messages.invoice.change_plan'),
            $description = trans('messages.change_plan.desc', [
                'plan' => $this->planGeneral->name,
                'newPlan' => $newPlan->name,
                'date' => $this->customer->formatDateTime(Carbon::parse($metadata['endsAt']), 'datetime_full'),
            ]),
            $customer_id = $this->customer->id,
            $currency_id = $this->planGeneral->currency_id,
            $billing_address = $this->customer->getDefaultBillingAddress(),
            $invoiceItems = [
                new InvoiceItem([
                    'item_id' => $this->id,
                    'item_type' => get_class($this),
                    'amount' => $metadata['amount'],
                    'title' => $newPlan->name,
                    'description' => view('plans._bill_desc', ['plan' => $newPlan]),
                ]),
            ],
        );

        // save subscription to invoice
        $invoice->subscription_id = $this->id;
        $invoice->new_plan_id = $newPlan->id;
        $invoice->save();

        // return
        return $invoice;
    }

    /**
     * Set subscription as ended.
     *
     * @return bool
     */
    public function setEnded()
    {
        // then set the sub end
        $this->status = self::STATUS_ENDED;
        $this->save();
    }

    public function setCancelled()
    {
        // then set the sub end
        $this->status = self::STATUS_CANCELLED;
        $this->save();
    }

    public function setTerminated()
    {
        // then set the sub end
        $this->status = self::STATUS_TERMINATED;
        $this->save();
    }

    /**
     * Get period by start date.
     *
     * @param  date  $date
     * @return date
     */
    public function getPeriodEndsAt($startDate)
    {
        return getPeriodEndsAt($startDate, $this->plan->frequency_amount, $this->plan->frequency_unit);
    }

    /**
     * Get trial period by start date.
     *
     * @param  date  $date
     * @return date
     */
    public function getTrialPeriodEndsAt($startDate)
    {
        return getPeriodEndsAt($startDate, $this->plan->trial_amount, $this->plan->trial_unit);
    }

    public function getAutoBillingDate()
    {
        return $this->current_period_ends_at->copy()->subDays(\Acelle\Model\Setting::get('subscription.auto_billing_period'));
    }

    /**
     * reach due date.
     */
    public function isBillingPeriod()
    {
        return Carbon::now()->greaterThanOrEqualTo(
            $this->getAutoBillingDate()
        );
    }

    /**
     * Change plan.
     */
    public function changePlan($newPlan)
    {
        // calculate change plan amout ends at
        $metadata = $this->calcChangePlan($newPlan);

        // new plan
        $this->plan_id = $newPlan->id;

        // new end period
        $this->current_period_ends_at = $metadata['endsAt'];

        $this->save();
    }

    /**
     * Associations.
     *
     * @var object | collect
     */
    public function subscriptionLogs()
    {
        // @todo dependency injection
        return $this->hasMany('\Acelle\Model\SubscriptionLog');
    }

    /**
     * Get all transactions from invoices.
     */
    public function transactions()
    {
        return \Acelle\Model\Transaction::whereIn('invoice_id', $this->invoices()->select('id'))
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc');
    }

    /**
     * Determine if the subscription is recurring and not on trial.
     *
     * @return bool
     */
    public function isRecurring()
    {
        return $this->is_recurring;
    }

    /**
     * Determine if the subscription is active.
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->status == self::STATUS_ACTIVE;
    }

    /**
     * Determine if the subscription is active.
     *
     * @return bool
     */
    public function isNew()
    {
        return $this->status == self::STATUS_NEW;
    }

    /**
     * Determine if the subscription is ended.
     *
     * @return bool
     */
    public function isEnded()
    {
        return $this->status == self::STATUS_ENDED;
    }

    public function isCancelled()
    {
        return $this->status == self::STATUS_CANCELLED;
    }

    public function isTerminated()
    {
        return $this->status == self::STATUS_TERMINATED;
    }


    /**
     * Determine if the subscription is pending.
     *
     * @return bool
     */
    public function activate()
    {
        if (!$this->isNew()) {
            throw new \Exception("Only new subscription can be activated, double check your code to make sure you call activate() on a new subscription");
        }

        if ($this->plan->hasTrial()) {
            $this->current_period_ends_at = $this->getTrialPeriodEndsAt(Carbon::now());
        } else {
            $this->current_period_ends_at = $this->getPeriodEndsAt(Carbon::now());
        }

        $this->status = self::STATUS_ACTIVE;
        $this->save();
    }

    /**
     * Next one period to subscription.
     *
     * @param  Gateway    $gateway
     * @return Boolean
     */
    public function nextPeriod()
    {
        return $this->getPeriodEndsAt($this->current_period_ends_at);
    }

    /**
     * Next one period to subscription.
     *
     * @param  Gateway    $gateway
     * @return Boolean
     */
    public function periodStartAt()
    {
        $startAt = $this->current_period_ends_at;
        $interval = $this->plan->frequency_unit;
        $intervalCount = $this->plan->frequency_amount;

        switch ($interval) {
            case 'month':
                $startAt = $startAt->subMonthsNoOverflow($intervalCount);
                break;
            case 'day':
                $startAt = $startAt->subDay($intervalCount);
                // no break
            case 'week':
                $startAt = $startAt->subWeek($intervalCount);
                break;
            case 'year':
                $startAt = $startAt->subYearsNoOverflow($intervalCount);
                break;
            default:
                $startAt = null;
        }

        return $startAt;
    }

    /**
     * Check if subscription is expired.
     *
     * @param  Int  $subscriptionId
     * @return date
     */
    public function isExpired()
    {
        // Get the current datetime
        $now = Carbon::now();

        // Check if $now is Greater or Equal to...
        return $now->gte($this->current_period_ends_at);
    }

    /**
     * Subscription transactions.
     *
     * @return array
     */
    public function getLogs()
    {
        return $this->subscriptionLogs()
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * Subscription transactions.
     *
     * @return array
     */
    public function addLog($type, $data, $transaction_id = null)
    {
        $log = new SubscriptionLog();
        $log->subscription_id = $this->id;
        $log->type = $type;
        $log->transaction_id = $transaction_id;
        $log->save();

        if (isset($data)) {
            $log->updateData($data);
        }

        return $log;
    }

    public function getItsOnlyUnpaidRenewInvoice()
    {
        if (!$this->isActive()) {
            throw new \Exception('Method getItsOnlyUnpaidRenewInvoice() only use for ACTIVE subscription!');
        }

        $query = $this->invoices()
            ->unpaid()
            ->renew();

        if ($query->count() > 1) {
            throw new \Exception('Somehow sub has more than one unpaid renew invoice!');
        }

        return $query->first() ? $query->first()->mapType() : $query->first();
    }

    /**
     * Cancel subscription. Set ends at to the end of period.
     *
     * @return void
     */
    public function disableRecurring()
    {
        if ($this->isEnded()) {
            throw new Exception('Subscription is ended. Can not change ended subscription state!');
        }

        if (!$this->isRecurring()) {
            throw new Exception('Subscription is not recurring. No need to disable again.');
        }

        \DB::transaction(function () {
            $this->is_recurring = false;
            $this->save();

            // không curring nữa nên xoá luôn NEW renew invoice hiện tại nếu có
            if ($this->getItsOnlyUnpaidRenewInvoice()) {
                $this->getItsOnlyUnpaidRenewInvoice()->delete();
            }

            // Log
            SubscriptionFacade::log($this, SubscriptionLog::TYPE_DISABLE_RECURRING, null, [
                'plan' => $this->getPlanName(),
            ]);
        });
    }

    /**
     * Cancel subscription. Set ends at to the end of period.
     *
     * @return void
     */
    public function enableRecurring()
    {
        \DB::transaction(function () {
            if ($this->isEnded()) {
                throw new Exception('Subscription is ended. Can not change ended subscription state!');
            }

            if ($this->isRecurring()) {
                throw new Exception('Subscription is recurring. No need to resume again.');
            }

            $this->is_recurring = true;
            $this->save();

            // Log
            SubscriptionFacade::log($this, SubscriptionLog::TYPE_ENABLE_RECURRING, null, [
                'plan' => $this->getPlanName(),
            ]);
        });
    }

    /**
     * Cancel subscription. Set ends at to the end of period.
     *
     * @return void
     */
    public function cancelNow()
    {
        if (!$this->isActive()) {
            throw new Exception('Subscription chỉ bị cancel now khi trạng thái là ACTIVE');
        }

        \DB::transaction(function () {
            // Log
            SubscriptionFacade::log($this, SubscriptionLog::TYPE_CANCEL_SUBSCRIPTION, null, [
                'plan' => $this->getPlanName(),
            ]);

            // Xoá NEW renew invoice hiện tại nếu có
            if ($this->getItsOnlyUnpaidRenewInvoice()) {
                $this->getItsOnlyUnpaidRenewInvoice()->delete();
            }

            // Xoá NEW change plan invoice hiện tại nếu có
            if ($this->getItsOnlyUnpaidChangePlanInvoice()) {
                $this->getItsOnlyUnpaidChangePlanInvoice()->delete();
            }

            // set status = ended. Lưu thời điểm bị end lại trong cột cancelled_at
            $this->setCancelled();
            $this->cancelled_at = \Carbon\Carbon::now();
            $this->save();
        });
    }

    public function terminate()
    {
        if (!$this->isActive() && !$this->isNew()) {
            throw new Exception('Subscription chỉ bị cancel now khi trạng thái là ACTIVE or NEW');
        }

        \DB::transaction(function () {
            // Xoá NEW init invoice hiện tại nếu có
            if ($this->isNew() && $this->getItsOnlyUnpaidInitInvoice()) {
                $this->getItsOnlyUnpaidInitInvoice()->delete();
            }

            // Xoá NEW renew invoice hiện tại nếu có
            if ($this->isActive() && $this->getItsOnlyUnpaidRenewInvoice()) {
                $this->getItsOnlyUnpaidRenewInvoice()->delete();
            }

            // Xoá NEW change plan invoice hiện tại nếu có
            if ($this->isActive() && $this->getItsOnlyUnpaidChangePlanInvoice()) {
                $this->getItsOnlyUnpaidChangePlanInvoice()->delete();
            }

            // set status = ended. Lưu thời điểm bị end lại trong cột cancelled_at
            $this->setTerminated();
            $this->terminated_at = \Carbon\Carbon::now();
            $this->save();

            // Log
            SubscriptionFacade::log($this, SubscriptionLog::TYPE_TERMINATE, null, [
                'plan' => $this->getPlanName(),
                'terminate_at' => $this->terminated_at,
            ]);
        });
    }

    public function end()
    {
        if (!$this->isActive()) {
            throw new Exception('Subscription chỉ end khi trạng thái là ACTIVE');
        }

        \DB::transaction(function () {
            // Xoá NEW renew invoice hiện tại nếu có
            if ($this->getItsOnlyUnpaidRenewInvoice()) {
                $this->getItsOnlyUnpaidRenewInvoice()->delete();
            }

            // Xoá NEW change plan invoice hiện tại nếu có
            if ($this->getItsOnlyUnpaidChangePlanInvoice()) {
                $this->getItsOnlyUnpaidChangePlanInvoice()->delete();
            }

            $this->setEnded();

            // Log
            SubscriptionFacade::log($this, SubscriptionLog::TYPE_END, null, [
                'plan' => $this->getPlanName(),
                'ends_at' => $this->current_period_ends_at,
            ]);
        });
    }

    public function endIfExpired()
    {
        if ($this->isActive() &&
            $this->isExpired() &&
            // Cancel immediately only if allowed due subscription setting is no
            \Acelle\Model\Setting::get('allowed_due_subscription') == 'no'
        ) {
            $this->end();
        }
    }

    public function checkAndCreateRenewInvoice()
    {
        if ($this->isActive() &&
            $this->isExpiring() && // đang hết hạn
            $this->canRenewPlan() && // check the function..
            $this->isRecurring() && // có recurring hàng kỳ
            // !$this->getItsOnlyUnpaidChangePlanInvoice() && // chưa có NEW change plan invoice nào cả, nhưng nếu có thì show 2 invoice ra luôn cũng dc
            !$this->getItsOnlyUnpaidRenewInvoice() // chưa có NEW renew invoice nào cả
        ) {
            \DB::transaction(function () {
                // tạo NEW renew invoice cho sub
                $invoice = $this->createRenewInvoice();

                // Log
                SubscriptionFacade::log($this, SubscriptionLog::TYPE_RENEW_INVOICE, $invoice->uid, [
                    'plan' => $this->getPlanName(),
                    'customer' => $this->getCustomerName(),
                    'amount' => $invoice->total(),
                ]);
            });
        }
    }

    /**
     * Renew subscription
     *
     * @return void
     */
    public function renew()
    {
        // set new current period
        $this->current_period_ends_at = $this->getPeriodEndsAt($this->current_period_ends_at);

        $this->save();
    }

    public function isExpiring()
    {
        if (!$this->current_period_ends_at) {
            return false;
        }

        // check if recurring accur
        if (Carbon::now()->greaterThanOrEqualTo($this->current_period_ends_at->copy()->subDays(Setting::get('subscription.expiring_period')))) {
            return true;
        }

        return false;
    }

    /**
     * Check if can renew free plan. amount > 0 or == 0
     *
     * @return void
     */
    public function canRenewPlan()
    {
        return ($this->plan->price > 0 ||
            $this->plan->isFree()
        );
    }

    public function canAutoRenewFreePlan()
    {
        return $this->plan->isFree();
    }

    /**
     * user want to change plan.
     *
     * @return bollean
     */
    public function calcChangePlan($plan)
    {
        if ($this->plan->isUnlimited()) {
            // temporarily disable this feature, otherwise, the plan's "unlimited" frequency_unit value will
            // cause code broken
            throw new Exception('Cannot change to a new plan if the current plan is unlimited');
        }

        if ($this->plan->id == $plan->id) {
            throw new Exception('The new plan is the same as current plan!');
        }

        if (($this->plan->frequency_unit != $plan->frequency_unit) ||
            ($this->plan->frequency_amount != $plan->frequency_amount) ||
            ($this->plan->currency->code != $plan->currency->code)
        ) {
            throw new \Exception(trans('messages.can_not_change_to_diff_currency_period_plan'));
        }

        // new ends at
        $newEndsAt = $this->current_period_ends_at;

        // amout per day of current plan
        $currentAmount = $this->plan->price;
        $periodDays = $this->current_period_ends_at->diffInDays($this->periodStartAt()->startOfDay());
        $remainDays = $this->current_period_ends_at->diffInDays(Carbon::now()->startOfDay());
        $currentPerDayAmount = ($currentAmount / $periodDays);
        $newAmount = ($plan->price / $periodDays) * $remainDays;
        $remainAmount = $currentPerDayAmount * $remainDays;

        $amount = $newAmount - $remainAmount;

        // if amount < 0
        if ($amount < 0) {
            $days = (int) ceil(-($amount / $currentPerDayAmount));
            $amount = 0;
            $newEndsAt->addDays($days);

            // if free plan
            if ($plan->price == 0) {
                $newEndsAt = $this->current_period_ends_at;
            }
        }

        return [
            'amount' => round($amount, 2),
            'endsAt' => $newEndsAt,
        ];
    }

    public function abortNew()
    {
        if (!$this->isNew()) {
            throw new \Exception('This subscription is not NEW. Can not abortNew!');
        }

        $this->getItsOnlyUnpaidInitInvoice()->delete();

        // if subscription is new -> cancel now subscription.
        // Make sure a new subscription must have a pending invoice
        $this->cancelNow();
    }

    public function getRemainingEmailCredits()
    {
        return $this->getSendEmailCreditTracker()->getRemainingCredits();
    }

    public function getCreditsLimit($name)
    {
        if ($name != 'send') {
            throw new Exception("`{$name}` credis limit is not available");
        }

        return $this->planGeneral->getOption('email_max');
    }

    public function getRemainingEmailCreditsPercentage()
    {
        // Temporarily use the RateTracker to get credits used as CreditTracker is not yet supported
        // Future: $creditsUsed = $this->getRemainingEmailCredits();
        $creditsUsed = $this->getSendEmailRateTracker()->getCreditsUsed();
        $creditsLimit = $this->planGeneral->getOption('email_max');

        // if = -1 (default:unlimited) return 0
        // if = 0 return 0
        if (!$creditsLimit || $creditsLimit == -1) {
            return 0;
        }

        $percentage = ($creditsLimit == RateLimit::UNLIMITED) ? 0 : $creditsUsed / $creditsLimit;

        return $percentage;
    }

    public static function createNewSubscription($customer, $plan)
    {
        $subscription = new self();
        $subscription->status = self::STATUS_NEW;
        $subscription->customer_id = $customer->id;
        $subscription->plan_id = $plan->id;
        $subscription->save();

        // $emailCredits = (int)$plan->getOption('email_max');
        // $subscription->getSendEmailCreditTracker()->setCredits($emailCredits);

        return $subscription;
    }

    public function deleteAndCleanup()
    {
        $this->invoices()->delete();
        $this->delete();
    }

    public static function scopeGeneral($query)
    {
        $query = $query->whereHas('planGeneral', function (Builder $query) {
            $query->general();
        });
    }

    public function getSendEmailRateTracker()
    {
        // Get the limits from plan
        $file = storage_path('app/quota/subscription-send-email-rate-tracking-log-'.$this->uid);
        $tracker = new RateTracker($file, $this->planGeneral->getSendEmailRateLimits());
        return $tracker;
    }

    public function getSendEmailCreditTracker($createFileIfNotExist = true)
    {
        $file = storage_path('app/quota/subscription-send-email-credits-'.$this->uid);
        return CreditTracker::load($file, $createFileIfNotExist);
    }

    public function getVerifyEmailCreditTracker($createFileIfNotExist = false)
    {
        // Temporarily not supported
        return null;
    }

    public function setDefaultEmailCredits()
    {
        // @important: temporarily set default credits to UNLIMITED
        // $defaultCredits = (int)$this->planGeneral->getOption('email_max');
        $defaultCredits = CreditTracker::UNLIMITED;
        $this->getSendEmailCreditTracker()->setCredits($defaultCredits);
    }
}
