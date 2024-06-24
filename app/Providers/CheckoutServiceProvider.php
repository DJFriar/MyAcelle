<?php

namespace Acelle\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Support\DeferrableProvider;
use Acelle\Library\BillingManager;
use Acelle\Library\Facades\Billing;
use Acelle\Cashier\Services\StripePaymentGateway;
use Acelle\Cashier\Services\OfflinePaymentGateway;
use Acelle\Cashier\Services\BraintreePaymentGateway;
use Acelle\Cashier\Services\CoinpaymentsPaymentGateway;
use Acelle\Cashier\Services\PaystackPaymentGateway;
use Acelle\Cashier\Services\PaypalPaymentGateway;
use Acelle\Cashier\Services\RazorpayPaymentGateway;
use Acelle\Model\Setting;

class CheckoutServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        // Register built-in services
        // Notice that the closure passed to the register() method is not actually executed during boot
        // It is to improve performance and to avoid executing DB queries in service providers
        Billing::register(OfflinePaymentGateway::TYPE, function () {
            $paymentInstruction = Setting::get('cashier.offline.payment_instruction');
            $service = new OfflinePaymentGateway($paymentInstruction);
            return $service;
        });

        Billing::register(StripePaymentGateway::TYPE, function () {
            $publishableKey = Setting::get('cashier.stripe.publishable_key');
            $secretKey = Setting::get('cashier.stripe.secret_key');
            $service = new StripePaymentGateway($publishableKey, $secretKey);
            return $service;
        });

        Billing::register(BraintreePaymentGateway::TYPE, function () {
            $environment = Setting::get('cashier.braintree.environment');
            $merchantId = Setting::get('cashier.braintree.merchant_id');
            $publicKey = Setting::get('cashier.braintree.public_key');
            $privateKey = Setting::get('cashier.braintree.private_key');
            $service = new BraintreePaymentGateway($environment, $merchantId, $publicKey, $privateKey);
            return $service;
        });

        Billing::register(CoinpaymentsPaymentGateway::TYPE, function () {
            $merchantId = Setting::get('cashier.coinpayments.merchant_id');
            $publicKey = Setting::get('cashier.coinpayments.public_key');
            $privateKey = Setting::get('cashier.coinpayments.private_key');
            $ipnSecret = Setting::get('cashier.coinpayments.ipn_secret');
            $receiveCurrency = Setting::get('cashier.coinpayments.receive_currency');
            $service = new CoinpaymentsPaymentGateway($merchantId, $publicKey, $privateKey, $ipnSecret, $receiveCurrency);
            return $service;
        });

        Billing::register(PaystackPaymentGateway::TYPE, function () {
            $publicKey = Setting::get('cashier.paystack.public_key');
            $secretKey = Setting::get('cashier.paystack.secret_key');
            $service = new PaystackPaymentGateway($publicKey, $secretKey);
            return $service;
        });

        Billing::register(PaypalPaymentGateway::TYPE, function () {
            $environment = Setting::get('cashier.paypal.environment');
            $clientId = Setting::get('cashier.paypal.client_id');
            $secret = Setting::get('cashier.paypal.secret');
            $service = new PaypalPaymentGateway($environment, $clientId, $secret);
            return $service;
        });

        Billing::register(RazorpayPaymentGateway::TYPE, function () {
            $keyId = Setting::get('cashier.razorpay.key_id');
            $keySecret = Setting::get('cashier.razorpay.key_secret');
            $service = new RazorpayPaymentGateway($keyId, $keySecret);
            return $service;
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(BillingManager::class, function ($app) {
            return new BillingManager(action('SubscriptionController@index'));
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [BillingManager::class];
    }
}
