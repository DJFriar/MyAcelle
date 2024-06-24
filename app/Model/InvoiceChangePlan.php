<?php

namespace Acelle\Model;

use Acelle\Model\Invoice;
use Acelle\Library\Facades\SubscriptionFacade;

class InvoiceChangePlan extends Invoice
{
    protected $table = 'invoices';

    public const TYPE_CHANGE_PLAN = 'change_plan';

    public function subscription()
    {
        return $this->belongsTo('Acelle\Model\Subscription');
    }

    public function newPlan()
    {
        return $this->belongsTo('Acelle\Model\Plan', 'new_plan_id');
    }

    /**
     * Process invoice.
     *
     * @return void
     */
    public function process()
    {
        // Xoá NEW renew invoice hiện tại nếu có
        if ($this->subscription->getItsOnlyUnpaidRenewInvoice()) {
            $this->subscription->getItsOnlyUnpaidRenewInvoice()->delete();
        }

        // change plan
        $this->subscription->changePlan($this->newPlan);

        // Handle business for different types of plan
        switch (get_class($this->subscription->plan->mapType())) {
            case PlanGeneral::class:

                // ....
                // ....

                break;

            default:

                throw new \Exception('Unknown plan type');

                break;
        }

        // Logging
        SubscriptionFacade::log($this->subscription, SubscriptionLog::TYPE_PAY_SUCCESS, $this->uid, [
            'amount' => $this->total(),
        ]);
    }

    /**
     * Get billing info.
     *
     * @return void
     */
    public function getBillingInfo()
    {
        $chargeInfo = trans('messages.bill.charge_now');

        return $this->getBillingInfoBase($chargeInfo, $this->newPlan);
    }

    public function checkoutAfterPayFailed($error)
    {
        SubscriptionFacade::log($this->subscription, SubscriptionLog::TYPE_PAY_FAILED, $this->uid, [
            'amount' => $this->total(),
            'error' => $error,
        ]);
    }

    public function beforeCancel()
    {
        SubscriptionFacade::log($this->subscription, SubscriptionLog::TYPE_CANCEL_INVOICE, $this->uid, [
            'amount' => $this->total(),
        ]);
    }

    public function refreshPrice()
    {
    }
}
