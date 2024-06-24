<?php

namespace Acelle\Library;

use Acelle\Model\Subscription;
use Acelle\Library\TransactionResult;
use Acelle\Model\SubscriptionLog;

class SubscriptionManager
{
    // Look for expired subscriptions and end it
    public function endExpiredSubscriptions()
    {
        $subscriptions = Subscription::active()->get();

        foreach ($subscriptions as $subscription) {
            $subscription->endIfExpired();
        }
    }

    // Look for expiring subscription and generate renew invoices
    public function createRenewInvoices()
    {
        $subscriptions = Subscription::active()->get();

        foreach ($subscriptions as $subscription) {
            $subscription->checkAndCreateRenewInvoice();
        }
    }

    // Auto pay renew invoices
    public function autoChargeRenewInvoices()
    {
        $renewInvoices = \Acelle\Model\InvoiceRenewSubscription::renew()->unpaid()->get();

        foreach ($renewInvoices as $invoice) {
            $subscription = $invoice->mapType()->subscription;
            $customer = $subscription->customer;

            // not reach due date
            if (!$subscription->isBillingPeriod()) {
                // do nothing
                continue;
            }

            // check if customer can auto charge
            if (!$customer->preferredPaymentGatewayCanAutoCharge()) {
                continue;
            }

            /**
             *  What if $customer->getPreferredPaymentGateway() is null?
             *  So, simply do not support bypass ZERO invoice
             *
            if ($invoice->total() == 0) {
                // Trường hợp invoice total = 0 thì pay nothing và set done luôn cho renew invoice
                $invoice->checkout($customer->getPreferredPaymentGateway(), function ($invoice) {
                    return new TransactionResult(TransactionResult::RESULT_DONE);
                });

                continue;
            }
            */

            // auto charge
            $customer->getPreferredPaymentGateway()->autoCharge($invoice);
        }
    }

    public function log($subscription, $type, $invoice_uid = null, $metadata = [])
    {
        $log = $subscription->subscriptionLogs()->create([
            'type' => $type,
            'invoice_uid' => $invoice_uid,
        ]);

        if (isset($metadata)) {
            $log->updateData($metadata);
        }

        return $log;
    }
}
