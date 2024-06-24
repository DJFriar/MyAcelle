<?php

namespace Acelle\Model;

use Acelle\Model\Invoice;
use Acelle\Library\Facades\SubscriptionFacade;

class InvoiceRenewSubscription extends Invoice
{
    protected $table = 'invoices';

    public const TYPE_RENEW_SUBSCRIPTION = 'renew_subscription';

    public function subscription()
    {
        return $this->belongsTo('Acelle\Model\Subscription');
    }

    /**
     * Process invoice.
     *
     * @return void
     */
    public function process()
    {
        // Xoá NEW change plan invoice hiện tại nếu có
        if ($this->subscription->getItsOnlyUnpaidChangePlanInvoice()) {
            $this->subscription->getItsOnlyUnpaidChangePlanInvoice()->delete();
        }

        /// renew
        $this->subscription->renew();

        // Handle business for different types of plan
        switch (get_class($this->subscription->plan->mapType())) {
            case PlanGeneral::class:

                // Refill sending credits
                $this->subscription->setDefaultEmailCredits();

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
        $chargeInfo = trans('messages.bill.charge_before', [
            'date' => $this->customer->formatDateTime($this->subscription->current_period_ends_at, 'datetime_full'),
        ]);
        $plan = $this->subscription->plan;

        return $this->getBillingInfoBase($chargeInfo, $plan);
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
        if ($this->isPaid()) {
            throw new \Exception("Can not change paid invoice price!");
        }

        $invoiceItem = $this->invoiceItems()->first(); // subscription plan always has 1 invoice item (design)
        if ($invoiceItem->amount != $this->subscription->plan->getPrice()) {
            $invoiceItem->amount = $this->subscription->plan->getPrice();
            $invoiceItem->save();
        }
    }
}
