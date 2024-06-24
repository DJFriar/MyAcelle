<?php

namespace Acelle\Http\Controllers\Admin;

use Illuminate\Http\Request;

use Acelle\Http\Requests;
use Acelle\Http\Controllers\Controller;
use Acelle\Model\Setting;
use Acelle\Model\PlanGeneral;
use Illuminate\Support\MessageBag;
use Acelle\Cashier\Cashier;
use Acelle\Model\Subscription;
use Acelle\Library\Facades\Billing;

class PaymentController extends Controller
{
    /**
     * Display all paymentt.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, MessageBag $message_bag)
    {
        $gateways = Billing::getGateways();

        if (!config('app.demo')) {
            $gateways = array_filter($gateways, function ($gateway) {
                return $gateway->getType() != 'coinpayments';
            });
        }

        return view('admin.payments.index', [
            'gateways' => $gateways,
        ]);
    }

    /**
     * Enable payment.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $name
     *
     * @return \Illuminate\Http\Response
     */
    public function enable(Request $request, $name)
    {
        // enable gateway
        Billing::enablePaymentGateway($name);

        $request->session()->flash('alert-success', trans('messages.payment_gateway.updated'));
        return redirect()->action('Admin\PaymentController@index');
    }

    /**
     * Disable payment.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $name
     *
     * @return \Illuminate\Http\Response
     */
    public function disable(Request $request, $name)
    {
        // disable gateway
        Billing::disablePaymentGateway($name);

        $request->session()->flash('alert-success', trans('messages.payment_gateway.updated'));
        return redirect()->action('Admin\PaymentController@index');
    }
}
