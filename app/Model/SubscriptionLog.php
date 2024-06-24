<?php

namespace Acelle\Model;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Acelle\Library\Traits\HasUid;

class SubscriptionLog extends Model
{
    use HasUid;

    public const TYPE_SELECT_PLAN = 'select_plan';
    public const TYPE_PAY_SUCCESS = 'pay_success';
    public const TYPE_PAY_FAILED = 'pay_failed';
    public const TYPE_ADMIN_APPROVE = 'admin_approve';
    public const TYPE_ADMIN_REJECT = 'admin_reject';
    public const TYPE_RENEW_INVOICE = 'renew_invoice';
    public const TYPE_CHANGE_PLAN_INVOICE = 'change_plan';
    public const TYPE_CANCEL_INVOICE = 'cancel_invoice';
    public const TYPE_CANCEL_SUBSCRIPTION = 'cancel_subscription';
    public const TYPE_DISABLE_RECURRING = 'disable_recurring';
    public const TYPE_ENABLE_RECURRING = 'enable_recurring';
    public const TYPE_END = 'end';
    public const TYPE_TERMINATE = 'terminate';

    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type', 'invoice_uid'
    ];

    /**
     * Associations.
     *
     * @var object | collect
     */
    public function subscription()
    {
        // @todo dependency injection
        return $this->belongsTo('\Acelle\Model\Subscription');
    }

    /**
     * Get metadata.
     *
     * @var object | collect
     */
    public function getData()
    {
        if (!$this->data) {
            return json_decode('{}', true);
        }

        return json_decode($this->data, true);
    }

    /**
     * Get metadata.
     *
     * @var object | collect
     */
    public function updateData($data)
    {
        $data = (object) array_merge((array) $this->getData(), $data);
        $this->data = json_encode($data);

        $this->save();
    }

    public function renderLog()
    {
        $data = $this->getData();

        switch($this->type) {
            case self::TYPE_SELECT_PLAN:
                return trans('messages.subscription.log.select_plan', [
                    'invoice' => $this->invoice_uid,
                    'plan' => $data['plan'],
                    'customer' => $data['customer'],
                    'amount' => $data['amount'],
                ]);
                break;
            case self::TYPE_PAY_SUCCESS:
                return trans('messages.subscription.log.pay_success', [
                    'invoice' => $this->invoice_uid,
                    'amount' => $data['amount'],
                ]);
                break;
            case self::TYPE_PAY_FAILED:
                return trans('messages.subscription.log.pay_failed', [
                    'invoice' => $this->invoice_uid,
                    'amount' => $data['amount'],
                    'error' => $data['error'],
                ]);
                break;
            case self::TYPE_ADMIN_APPROVE:
                return trans('messages.subscription.log.admin_approve', [
                    'invoice' => $this->invoice_uid,
                    'amount' => $data['amount'],
                ]);
                break;
            case self::TYPE_ADMIN_REJECT:
                return trans('messages.subscription.log.admin_reject', [
                    'invoice' => $this->invoice_uid,
                    'amount' => $data['amount'],
                    'reason' => $data['reason'],
                ]);
                break;
            case self::TYPE_RENEW_INVOICE:
                return trans('messages.subscription.log.renew_invoice', [
                    'invoice' => $this->invoice_uid,
                    'amount' => $data['amount'],
                ]);
                break;
            case self::TYPE_CHANGE_PLAN_INVOICE:
                return trans('messages.subscription.log.change_plan_invoice', [
                    'invoice' => $this->invoice_uid,
                    'plan' => $data['plan'],
                    'new_plan' => $data['new_plan'],
                    'amount' => $data['amount'],
                ]);
                break;
            case self::TYPE_CANCEL_INVOICE:
                return trans('messages.subscription.log.cancel_invoice', [
                    'invoice' => $this->invoice_uid,
                    'amount' => $data['amount'],
                ]);
                break;
            case self::TYPE_CANCEL_SUBSCRIPTION:
                return trans('messages.subscription.log.cancel_subscription', [
                    'plan' => $data['plan'],
                ]);
                break;
            case self::TYPE_DISABLE_RECURRING:
                return trans('messages.subscription.log.disable_recurring', [
                    'plan' => $data['plan'],
                ]);
                break;
            case self::TYPE_ENABLE_RECURRING:
                return trans('messages.subscription.log.enable_recurring', [
                    'plan' => $data['plan'],
                ]);
                break;
            case self::TYPE_END:
                return trans('messages.subscription.log.end', [
                    'plan' => $data['plan'],
                    'ends_at' => $data['ends_at'],
                ]);
                break;
            case self::TYPE_TERMINATE:
                return trans('messages.subscription.log.terminate', [
                    'plan' => $data['plan'],
                    'terminate_at' => $data['terminate_at'],
                ]);
                break;
            default:
                throw new \Exception("Log type $this->type is not found!");
        }
    }
}
