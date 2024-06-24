<?php

/**
 * Plan class.
 *
 * Model class for Plan
 *
 * LICENSE: This product includes software developed at
 * the Acelle Co., Ltd. (http://acellemail.com/).
 *
 * @category   MVC Model
 *
 * @author     N. Pham <n.pham@acellemail.com>
 * @author     L. Pham <l.pham@acellemail.com>
 * @copyright  Acelle Co., Ltd
 * @license    Acelle Co., Ltd
 *
 * @version    1.0
 *
 * @link       http://acellemail.com
 */

namespace Acelle\Model;

use Illuminate\Database\Eloquent\Model;
use Acelle\Library\Traits\HasUid;
use Acelle\Library\RateLimit;

class Plan extends Model
{
    use HasUid;

    // Plan status
    public const STATUS_INACTIVE = 'inactive';
    public const STATUS_ACTIVE = 'active';

    public const UNLIMITED_TIME = 'unlimited';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'frequency_amount',
        'frequency_unit',
        'price',
        'currency_id',
        'trial_amount',
        'trial_unit',
    ];

    public function mapType()
    {
        //
        switch ($this->type) {
            case \Acelle\Model\PlanGeneral::TYPE_GENERAL:
                return \Acelle\Model\PlanGeneral::find($this->id);
                break;
            case \Acelle\Model\PlanNumber::TYPE_NUMBER:
                return \Acelle\Model\PlanNumber::find($this->id);
                break;
            case \Acelle\Model\PlanSenderId::TYPE_SENDER_ID:
                return \Acelle\Model\PlanSenderId::find($this->id);
                break;
            case \Acelle\Model\PlanKeyword::TYPE_KEYWORD:
                return \Acelle\Model\PlanKeyword::find($this->id);
                break;
            default:
                throw new \Exception("Plan type #{$this->type} not found!");
        }
    }

    public static function findByUid($uid)
    {
        $first = self::where('uid', '=', $uid)->first();
        return $first ? $first->mapType() : $first;
    }

    /**
     * The rules for validation.
     *
     * @var array
     */
    public function rules()
    {
        $rules = array(
            'name' => 'required',
            'currency_id' => 'required',
            'frequency_amount' => 'required|min:1',
            'frequency_unit' => 'required',
            'price' => 'required|min:0',
        );

        return $rules;
    }

    public function admin()
    {
        return $this->belongsTo('Acelle\Model\Admin');
    }

    public function currency()
    {
        return $this->belongsTo('Acelle\Model\Currency');
    }

    public function subscriptions()
    {
        return $this->hasMany('Acelle\Model\Subscription', 'plan_id');
    }

    public function activeSubscriptions()
    {
        return $this->subscriptions()->active();
    }

    /**
     * Frequency time unit options.
     *
     * @return array
     */
    public static function timeUnitOptions()
    {
        return [
            ['value' => 'day', 'text' => trans('messages.day')],
            ['value' => 'week', 'text' => trans('messages.week')],
            ['value' => 'month', 'text' => trans('messages.month')],
            ['value' => 'year', 'text' => trans('messages.year')],
        ];
    }

    /**
     * Get billing recurs available values.
     *
     * @return array
     */
    public static function billingCycleValues()
    {
        return [
            'daily' => [
                'frequency_amount' => 1,
                'frequency_unit' => 'day',
            ],
            'monthly' => [
                'frequency_amount' => 1,
                'frequency_unit' => 'month',
            ],
            'yearly' => [
                'frequency_amount' => 1,
                'frequency_unit' => 'year',
            ],
        ];
    }

    // For compatibility with anothe plugin
    public static function getAvailablePlans()
    {
        return self::active()->available()->get();
    }

    /**
     * Get billing recurs select options.
     *
     * @return array
     */
    public function getBillingCycleSelectOptions()
    {
        $options = [];

        foreach (self::billingCycleValues() as $key => $data) {
            $wording = trans('messages.plan.billing_cycle.'.$key);
            $options[] = ['text' => $wording, 'value' => $key];
        }

        // exist
        if ($this->getOption('billing_cycle') == 'other') {
            $wording = trans('messages.plan.billing_cycle.phrase', [
                'frequency_amount' => number_with_delimiter($this->getFrequencyAmount()),
                'frequency_unit' => $this->getFrequencyUnit(),
            ]);

            $options[] = ['text' => $wording, 'value' => 'other'];
        }

        // Custom
        $options[] = ['text' => trans('messages.plan.billing_cycle.custom'), 'value' => 'custom'];

        return $options;
    }

    /**
     * Get customer select2 select options.
     *
     * @return array
     */
    public static function select2($request)
    {
        $data = ['items' => [], 'more' => true];

        $query = self::active();
        if (isset($request->q)) {
            $keyword = $request->q;
            $query = $query->where(function ($q) use ($keyword) {
                $q->orwhere('plans.name', 'like', '%'.$keyword.'%');
            });
        }

        // Read all check
        if ($request->user()->admin && !$request->user()->admin->can('readAll', \Acelle\Model\PlanGeneral::newGeneral())) {
            $query = $query->where('plans.admin_id', '=', $request->user()->admin->id);
        }


        // Only allow users to change to a plan with the same PERIOD
        if ($request->change_from_uid) {
            $plan = \Acelle\Model\PlanGeneral::findByUid($request->change_from_uid);

            $query = $query->where('id', '<>', $request->change_from_uid);
            $query = $query->where('uid', '<>', $request->change_from_uid);
            $query = $query->where('frequency_amount', '=', $plan->getFrequencyAmount());
            $query = $query->where('frequency_unit', '=', $plan->getFrequencyUnit());
        }

        foreach ($query->limit(20)->get() as $plan) {
            $data['items'][] = ['id' => $plan->uid, 'text' => htmlspecialchars($plan->name).'|||'.htmlspecialchars(\Acelle\Library\Tool::format_price($plan->getPrice(), $plan->currency->format))];
        }

        return json_encode($data);
    }

    public function displayFrequencyTime()
    {
        return trans('messages.plan.period', [
            'amount' => number_with_delimiter($this->getFrequencyAmount()),
            'unit' => trans('messages.' . \Acelle\Library\Tool::getPluralPrase($this->getFrequencyUnit(), $this->getFrequencyAmount())),
        ]);
    }

    /**
     * Subscriptions count.
     *
     * @return int
     */
    public function subscriptionsCount()
    {
        return $this->subscriptions()->count();
    }

    /**
     * Customers count.
     *
     * @return int
     */
    public function customersCount()
    {
        return $this->activeSubscriptions()->count();
    }

    /**
     * Check if plan is free.
     *
     * @return bool
     */
    public function isFree()
    {
        return $this->price == 0;
    }

    /**
     * PlanInterface: get interval count.
     *
     * @return string
     */
    public function getFormattedPrice()
    {
        return \Acelle\Library\Tool::format_price($this->price, $this->currency->format);
    }

    /**
     * Check if plan is active.
     *
     * @var bool
     */
    public function isActive()
    {
        return $this->status == self::STATUS_ACTIVE;
    }

    /**
     * Show plan.
     *
     * @return bool
     */
    public function visibleOff()
    {
        $this['visible'] = 0;
        $this->save();
    }

    /**
     * Hide plan.
     *
     * @return bool
     */
    public function visibleOn()
    {
        $this['visible'] = true;
        $this->save();
    }

    /**
     * Disable plan.
     *
     * @return bool
     */
    public function disable()
    {
        $this->status = self::STATUS_INACTIVE;
        $this['visible'] = false;

        return $this->save();
    }

    /**
     * Enable plan.
     *
     * @return bool
     */
    public function enable()
    {
        $this->status = self::STATUS_ACTIVE;

        return $this->save();
    }

    public function scopeActive($query)
    {
        $query = $query->where('plans.status', '=', self::STATUS_ACTIVE);
    }

    public function scopeAvailable($query)
    {
        $query = $query->where('plans.visible', '=', true);
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function getSendEmailRateLimits()
    {
        $limits = [];
        $options = $this->getOptions();

        $timeValue = $options['sending_quota_time'];
        if ($timeValue != RateLimit::UNLIMITED) {
            $timeUnit = $options['sending_quota_time_unit'];
            $limit = $options['sending_quota'];

            $limits[] = new RateLimit(
                $limit,
                $timeValue,
                $timeUnit,
                $description = "Sending limit of {$limit} per {$timeValue} {$timeUnit}"
            );
        }

        return $limits;
    }

    public function updateTos($params)
    {
        $rules = [];
        if ($params['terms_of_service']['enabled'] == 'no') {
            $rules['terms_of_service.content'] = 'required';
        }

        // make validator
        $validator = \Validator::make($params, $rules);

        // redirect if fails
        if ($validator->fails()) {
            return $validator;
        }

        \Acelle\Model\Setting::set('terms_of_service.enabled', $params['terms_of_service']['enabled']);
        \Acelle\Model\Setting::set('terms_of_service.content', $params['terms_of_service']['content']);

        return $validator;
    }

    public function hasTrial()
    {
        return $this->trial_amount > 0;
    }

    public function getTrialPeriodTimePhrase()
    {
        return trans_choice('messages.' . $this->getTrialUnit() . '.choice', $this->getTrialAmount(), [
            'amount' => $this->trial_amount,
        ]);
    }

    public function getFrequencyAmount()
    {
        return $this->frequency_amount;
    }

    public function getFrequencyUnit()
    {
        return $this->frequency_unit;
    }

    public function getTrialAmount()
    {
        return $this->trial_amount;
    }

    public function getTrialUnit()
    {
        return $this->trial_unit;
    }

    public function isUnlimited()
    {
        return $this->frequency_unit == self::UNLIMITED_TIME;
    }

    public function allowSenderVerification()
    {
        if ($this->useSystemSendingServer()) {
            $server = $this->primarySendingServer();
            if ($server && ($server->allowVerifyingOwnEmailsRemotely() || $server->allowVerifyingOwnEmails())) {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }
}
