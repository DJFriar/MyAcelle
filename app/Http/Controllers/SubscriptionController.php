<?php

namespace Acelle\Http\Controllers;

use Illuminate\Http\Request;
use Acelle\Model\PlanGeneral;
use Acelle\Model\SubscriptionLog;
use Acelle\Library\Facades\Billing;
use Acelle\Library\Facades\SubscriptionFacade;
use Acelle\Model\InvoiceNewSubscription;
use Acelle\Model\Transaction;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        // Trick here: these tasks are supposed to be executed in the background only
        SubscriptionFacade::endExpiredSubscriptions();
        SubscriptionFacade::createRenewInvoices();
        SubscriptionFacade::autoChargeRenewInvoices();

        // init
        $customer = $request->user()->customer;
        $subscription = $customer->getNewOrActiveGeneralSubscription();

        // 1. HAVE NOT HAD NEW/ACTIVE SUBSCRIPTION YET
        if (!$subscription) {
            // User chưa có subscription sẽ được chuyển qua chọn plan
            return redirect()->action('SubscriptionController@selectPlan');
        }

        // 2. IF PLAN NOT ACTIVE
        if (!$subscription->planGeneral->isActive()) {
            return response()->view('errors.general', [ 'message' => __('messages.subscription.error.plan-not-active', [ 'name' => $subscription->planGeneral->name]) ]);
        }

        // 3. SUBSCRIPTION IS NEW
        if ($subscription->isNew()) {
            $invoice = $subscription->getItsOnlyUnpaidInitInvoice();

            return redirect()->action('SubscriptionController@payment', [
                'invoice_uid' => $invoice->uid,
            ]);
        }

        // 3. SUBSCRIPTION IS ACTIVE, SHOW DETAILS PAGE
        return view('subscription.index', [
            'subscription' => $subscription,
            'plan' => $subscription->planGeneral,
        ]);
    }

    public function selectPlan(Request $request)
    {
        // init
        $customer = $request->user()->customer;
        $subscription = $customer->getNewOrActiveGeneralSubscription();

        return view('subscription.selectPlan', [
            'plans' => PlanGeneral::getAvailableGeneralPlans(),
            'subscription' => $subscription,
            'getLastCancelledOrEndedGeneralSubscription' => $customer->getLastCancelledOrEndedGeneralSubscription(),
        ]);
    }

    public function assignPlan(Request $request)
    {
        $customer = $request->user()->customer;
        $plan = PlanGeneral::findByUid($request->plan_uid);

        // already has subscription
        if ($customer->getCurrentActiveGeneralSubscription()) {
            throw new \Exception('Customer already has active subscription!');
        }

        // subscription hiện đang new. Customer muốn thay đổi plan khác?
        // delete luôn subscription
        $current = $customer->subscriptions()->general()->newOrActive()->first();
        if ($current) {
            $current->deleteAndCleanup();
        }

        // assign plan
        $subscription = $customer->assignGeneralPlan($plan);

        // Check if subscriotion is new
        return redirect()->action('SubscriptionController@billingInformation', [
            'invoice_uid' => $subscription->getItsOnlyUnpaidInitInvoice()->uid,
        ]);
    }

    public function billingInformation(Request $request)
    {
        $customer = $request->user()->customer;
        $invoice = $customer->invoices()->where('uid', '=', $request->invoice_uid)->first();
        $billingAddress = $customer->getDefaultBillingAddress();

        // can not found the invoice
        if (!$invoice) {
            return redirect()->action('SubscriptionController@index')
                ->with('alert-warning', "The invoice with ID [{$request->invoice_uid}] does not exist!");
        }

        // can not found the invoice
        if ($invoice->isPaid()) {
            // throw new \Exception("The invoice with ID [{$request->invoice_uid}] is paid!");
            return redirect()->action('SubscriptionController@index')
                ->with('alert-warning', "The invoice with ID [{$request->invoice_uid}] is paid!");
        }

        // always update invoice price from plan
        $invoice->mapType()->refreshPrice();

        // Save posted data
        if ($request->isMethod('post')) {
            $validator = $invoice->updateBillingInformation($request->all());

            // redirect if fails
            if ($validator->fails()) {
                return response()->view('subscription.billingInformation', [
                    'invoice' => $invoice,
                    'billingAddress' => $billingAddress,
                    'errors' => $validator->errors(),
                ], 400);
            }

            // Khúc này customer cập nhật thông tin billing information cho lần tiếp theo
            $customer->updateBillingInformationFromInvoice($invoice);

            $request->session()->flash('alert-success', trans('messages.billing_address.updated'));

            // return to subscription
            return redirect()->action('SubscriptionController@payment', [
                'invoice_uid' => $invoice->uid,
            ]);
        }

        return view('subscription.billingInformation', [
            'invoice' => $invoice,
            'billingAddress' => $billingAddress,
        ]);
    }

    public function payment(Request $request)
    {
        // Get current customer
        $customer = $request->user()->customer;

        // get invoice
        $invoice = $customer->invoices()->where('uid', '=', $request->invoice_uid)->first();

        // can not found the invoice
        if (!$invoice) {
            // throw new \Exception("The invoice with ID [{$request->invoice_uid}] does not exist!");
            return redirect()->action('SubscriptionController@index')
                ->with('alert-warning', "The invoice with ID [{$request->invoice_uid}] does not exist!");
        }

        // can not found the invoice
        if ($invoice->isPaid()) {
            // throw new \Exception("The invoice with ID [{$request->invoice_uid}] is paid!");
            return redirect()->action('SubscriptionController@index')
                ->with('alert-warning', "The invoice with ID [{$request->invoice_uid}] is paid!");
        }

        // no unpaid invoice found
        if (!$invoice) {
            // throw new \Exception('Can not find unpaid invoice with id:' . $request->invoice_uid);
            // just redirect to index
            return redirect()->action('SubscriptionController@index');
        }

        // always update invoice price from plan
        $invoice->mapType()->refreshPrice();

        // nếu đang có pending transaction thì luôn show màn hình pending
        if ($invoice->getPendingTransaction()) {
            return view('subscription.pending', [
                'invoice' => $invoice,
                'transaction' => $invoice->getPendingTransaction(),
            ]);
        }

        // luôn luôn require billing information
        if (!$invoice->hasBillingInformation()) {
            return redirect()->action('SubscriptionController@billingInformation', [
                'invoice_uid' => $invoice->uid,
            ]);
        }

        return view('subscription.payment', [
            'invoice' => $invoice,
        ]);
    }

    public function checkout(Request $request)
    {
        $customer = $request->user()->customer;
        $invoice = $customer->invoices()->where('uid', '=', $request->invoice_uid)->first()->mapType();

        // can not found the invoice
        if (!$invoice) {
            return redirect()->action('SubscriptionController@index')
                ->with('alert-warning', "The invoice with ID [{$request->invoice_uid}] does not exist!");
        }

        // can not found the invoice
        if ($invoice->isPaid()) {
            // throw new \Exception("The invoice with ID [{$request->invoice_uid}] is paid!");
            return redirect()->action('SubscriptionController@index')
                ->with('alert-warning', "The invoice with ID [{$request->invoice_uid}] is paid!");
        }

        // always update invoice price from plan
        $invoice->mapType()->refreshPrice();

        // Luôn đặt payment method mặc định cho customer là lần chọn payment gần nhất
        $request->user()->customer->updatePaymentMethod([
            'method' => $request->payment_method,
        ]);

        // Bỏ qua việc nhập card information khi subscribe plan with trial
        if (
            \Acelle\Model\Setting::get('not_require_card_for_trial') == 'yes' &&
            $invoice->type == InvoiceNewSubscription::TYPE_NEW_SUBSCRIPTION && // chỉ có newSub invoice mới bỏ qua trial
            in_array($customer->getPreferredPaymentGateway()->getType(), ['stripe', 'braintree', 'paystack']) && // @todo moving this to interface
            $invoice->getPlan()->hasTrial()
        ) {
            $invoice->checkout($customer->getPreferredPaymentGateway(), function () {
                return new \Acelle\Library\TransactionResult(\Acelle\Library\TransactionResult::RESULT_DONE);
            });

            return redirect()->action('SubscriptionController@index');
        }

        // redirect to service checkout
        return redirect()->away($customer->getPreferredPaymentGateway()->getCheckoutUrl($invoice));
    }

    public function cancelInvoice(Request $request, $uid)
    {
        $invoice = \Acelle\Model\Invoice::findByUid($uid);

        // return to select plan if sub is NEW
        if ($request->user()->customer->getNewGeneralSubscription()) {
            return redirect()->action('SubscriptionController@selectPlan');
        }

        if (!$request->user()->customer->can('delete', $invoice)) {
            return $this->notAuthorized();
        }

        $invoice->cancel();

        // Redirect to my subscription page
        $request->session()->flash('alert-success', trans('messages.invoice.cancelled'));
        return redirect()->action('SubscriptionController@index');
    }

    /**
     * Change plan.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     **/
    public function changePlan(Request $request)
    {
        $customer = $request->user()->customer;
        $subscription = $customer->getCurrentActiveGeneralSubscription();
        $gateway = $customer->getPreferredPaymentGateway();
        $plans = PlanGeneral::getAvailableGeneralPlans();

        // Authorization
        if (!$request->user()->customer->can('changePlan', $subscription)) {
            return $this->notAuthorized();
        }

        //
        if ($request->isMethod('post')) {
            $newPlan = PlanGeneral::findByUid($request->plan_uid);

            try {
                $changePlanInvoice = null;

                \DB::transaction(function () use ($subscription, $newPlan, &$changePlanInvoice) {
                    // set invoice as pending
                    $changePlanInvoice = $subscription->createChangePlanInvoice($newPlan);

                    // Log
                    SubscriptionFacade::log($subscription, SubscriptionLog::TYPE_CHANGE_PLAN_INVOICE, $changePlanInvoice->uid, [
                        'plan' => $subscription->getPlanName(),
                        'new_plan' => $newPlan->name,
                        'amount' => $changePlanInvoice->total(),
                    ]);
                });

                // return to subscription
                return redirect()->action('SubscriptionController@payment', [
                    'invoice_uid' => $changePlanInvoice->uid,
                ]);
            } catch (\Exception $e) {
                $request->session()->flash('alert-error', $e->getMessage());
                return redirect()->action('SubscriptionController@index');
            }
        }

        return view('subscription.change_plan', [
            'subscription' => $subscription,
            'gateway' => $gateway,
            'plans' => $plans,
        ]);
    }

    /**
     * Cancel subscription at the end of current period.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function disableRecurring(Request $request)
    {
        if (isSiteDemo()) {
            return response()->json(["message" => trans('messages.operation_not_allowed_in_demo')], 404);
        }

        $customer = $request->user()->customer;
        $subscription = $customer->getNewOrActiveGeneralSubscription();

        if ($request->user()->customer->can('disableRecurring', $subscription)) {
            $subscription->disableRecurring();
        }

        // Redirect to my subscription page
        $request->session()->flash('alert-success', trans('messages.subscription.disabled_recurring'));
        return redirect()->action('SubscriptionController@index');
    }


    /**
     * Cancel subscription at the end of current period.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function enableRecurring(Request $request)
    {
        $customer = $request->user()->customer;
        $subscription = $customer->getNewOrActiveGeneralSubscription();

        if ($request->user()->customer->can('enableRecurring', $subscription)) {
            $subscription->enableRecurring();
        }

        // Redirect to my subscription page
        $request->session()->flash('alert-success', trans('messages.subscription.enabled_recurring'));
        return redirect()->action('SubscriptionController@index');
    }

    /**
     * Cancel now subscription at the end of current period.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function cancelNow(Request $request)
    {
        if (isSiteDemo()) {
            return response()->json(["message" => trans('messages.operation_not_allowed_in_demo')], 404);
        }

        $customer = $request->user()->customer;
        $subscription = $customer->getNewOrActiveGeneralSubscription();

        if ($request->user()->customer->can('cancelNow', $subscription)) {
            $subscription->cancelNow();
        }

        // Redirect to my subscription page
        $request->session()->flash('alert-success', trans('messages.subscription.cancelled_now'));
        return redirect()->action('SubscriptionController@index');
    }

    public function orderBox(Request $request)
    {
        $customer = $request->user()->customer;

        // get unpaid invoice
        $invoice = $customer->invoices()->unpaid()->where('uid', '=', $request->invoice_uid)->first()->mapType();

        // gateway fee
        if ($request->payment_method) {
            $gateway = Billing::getGateway($request->payment_method);

            // update invoice fee if trial and gatewaye need minimal fee for auto billing
            $invoice->updatePaymentServiceFee($gateway);
        }

        return view('subscription.orderBox', [
            'subscription' => $invoice->subscription,
            'bill' => $invoice->mapType()->getBillingInfo(),
            'invoice' => $invoice,
        ]);
    }

    public function verifyPendingTransaction(Request $request, $invoice_uid)
    {
        $invoice = \Acelle\Model\Invoice::findByUid($invoice_uid);
        $transaction = $invoice->getPendingTransaction();

        // invoice đã paid thì trả về subscription page.
        if ($invoice->isPaid()) {
            return redirect()->action('SubscriptionController@index');
        }

        // không có pending transaction
        if (!$transaction) {
            throw new \Exception('Invoice này không có pending transaction! kiểm tra lại UI xem có trường hợp nào không có pending transaction mà qua đây không?');
        }

        // get gateway
        $gateway = Billing::getGateway($transaction->method);

        // gateway verify transaction from invoice
        try {
            // get transaction result from service
            $result = $gateway->verify($transaction);

            // handle transaction result
            $invoice->handleTransactionResult($result);
        } catch (\Throwable $e) {
            $request->session()->flash('alert-error', $e->getMessage());
            return redirect()->action('SubscriptionController@payment', [
                'invoice_uid' => $invoice->uid,
            ]);
        }

        //
        if ($result->isDone()) {
            return redirect()->action('SubscriptionController@index');
        } else {
            $request->session()->flash('alert-info', trans('messages.subscription.payment_status.refreshed'));

            return redirect()->action('SubscriptionController@payment', [
                'invoice_uid' => $invoice->uid,
            ]);
        }
    }
}
