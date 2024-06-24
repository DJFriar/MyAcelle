<?php

namespace Acelle\Model;

use Illuminate\Database\Eloquent\Model;

use Acelle\Model\Subscription;
use Acelle\Model\Transaction;
use Acelle\Library\Traits\HasUid;
use Dompdf\Dompdf;
use Acelle\Library\StringHelper;
use Acelle\Library\Facades\SubscriptionFacade;
use Exception;
use Closure;
use Acelle\Library\Contracts\PaymentGatewayInterface;

use function Acelle\Helpers\getAppHost;

class Invoice extends Model
{
    use HasUid;

    // statuses
    public const STATUS_NEW = 'new';               // unpaid
    public const STATUS_PAID = 'paid';

    // type
    // public const TYPE_RENEW_SUBSCRIPTION = 'renew_subscription';
    // public const TYPE_NEW_SUBSCRIPTION = 'new_subscription';
    // public const TYPE_CHANGE_PLAN = 'change_plan';
    // public const TYPE_TOP_UP = 'top_up';

    protected $fillable = [
        'billing_first_name',
        'billing_last_name',
        'billing_address',
        'billing_email',
        'billing_phone',
        'billing_country_id',
    ];

    public static function findByUid($uid)
    {
        $first = self::where('uid', '=', $uid)->first();
        return $first ? $first->mapType() : $first;
    }

    public function scopeNew($query)
    {
        $query->whereIn('status', [
            self::STATUS_NEW,
        ]);
    }

    public function scopeUnpaid($query)
    {
        $query->whereIn('status', [
            self::STATUS_NEW,
        ]);
    }

    public function scopePaid($query)
    {
        $query->whereIn('status', [
            self::STATUS_PAID,
        ]);
    }

    public function scopePending($query)
    {
        $query->whereHas('transactions', function ($q) {
            $q->pending();
        });
    }

    public function scopeNotPending($query)
    {
        $query->orWhereDoesntHave('transactions', function ($q) {
            $q->pending();
        });
    }

    public function scopeChangePlan($query)
    {
        $query->where('type', InvoiceChangePlan::TYPE_CHANGE_PLAN);
    }

    public function scopeNewSubscription($query)
    {
        $query->whereIn('type', [
            InvoiceNewSubscription::TYPE_NEW_SUBSCRIPTION,
        ]);
    }

    public function scopeRenew($query)
    {
        $query->where('type', InvoiceRenewSubscription::TYPE_RENEW_SUBSCRIPTION);
    }

    public function scopeTopUp($query)
    {
        $query->where('type', InvoiceTopUp::TYPE_TOP_UP);
    }

    /**
     * Invoice currency.
     */
    public function currency()
    {
        return $this->belongsTo('Acelle\Model\Currency');
    }

    /**
     * Invoice customer.
     */
    public function customer()
    {
        return $this->belongsTo('Acelle\Model\Customer');
    }

    /**
     * Invoice items.
     */
    public function invoiceItems()
    {
        return $this->hasMany('Acelle\Model\InvoiceItem', 'invoice_id');
    }

    /**
     * Transactions.
     */
    public function transactions()
    {
        return $this->hasMany('Acelle\Model\Transaction', 'invoice_id');
    }

    public function billingCountry()
    {
        return $this->belongsTo('Acelle\Model\Country', 'billing_country_id');
    }

    /**
     * Get pending transaction.
     */
    public function getPendingTransaction()
    {
        $query = $this->transactions()
            ->where('status', Transaction::STATUS_PENDING)
            ->orderBy('created_at', 'desc');

        // invoice only has one pending transaction
        if ($query->count() > 1) {
            throw new Exception("Invoice #{$this->uid} should has only on pending transaction!");
        }

        return $query->first();
    }

    /**
     * Last transaction.
     */
    public function lastTransaction()
    {
        return $this->transactions()
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->first();
    }

    /**
     * Last transaction is failed.
     */
    public function lastTransactionIsFailed()
    {
        if ($this->lastTransaction()) {
            return $this->lastTransaction()->isFailed();
        } else {
            return false;
        }
    }

    /**
     * Set as pending.
     *
     * @return void
     */
    public function setPending()
    {
        $this->status = self::STATUS_PENDING;
        $this->save();
    }

    /**
     * Set as paid.
     *
     * @return void
     */
    public function setPaid()
    {
        $this->status = self::STATUS_PAID;
        $this->save();
    }

    public function getTax()
    {
        $total = 0;

        foreach ($this->invoiceItems as $item) {
            $total += $item->getTax();
        }

        return $total;
    }

    public function subTotal()
    {
        $total = 0;

        foreach ($this->invoiceItems as $item) {
            $total += $item->subTotal();
        }

        return $total;
    }

    public function total()
    {
        $total = 0;

        foreach ($this->invoiceItems as $item) {
            $total += $item->total();
        }

        return $total + $this->fee;
    }

    /**
     * Check new.
     *
     * @return void
     */
    public function isNew()
    {
        return $this->status == self::STATUS_NEW;
    }

    /**
     * set status as new.
     *
     * @return void
     */
    public function setNew()
    {
        $this->status = self::STATUS_NEW;
        $this->save();
    }

    /**
     * Approve invoice.
     *
     * @return void
     */
    public function approve()
    {
        // for only new invoice
        if (!$this->isNew()) {
            throw new \Exception("Trying to approve an invoice that is not NEW (Invoice ID: {$this->id}, status: {$this->status}");
        }

        // for only new invoice
        if (!$this->getPendingTransaction()) {
            throw new \Exception("Trying to approve an invoice that does not have a pending transaction (Invoice ID: {$this->id}, status: {$this->status}");
        }

        \DB::transaction(function () {
            // fulfill invoice
            $this->paySuccess();
        });
    }

    /**
     * Reject invoice.
     *
     * @return void
     */
    public function reject($error)
    {
        // for only new invoice
        if (!$this->isNew()) {
            throw new \Exception("Trying to approve an invoice that is not NEW (Invoice ID: {$this->id}, status: {$this->status}");
        }

        // for only new invoice
        if (!$this->getPendingTransaction()) {
            throw new \Exception("Trying to approve an invoice that does not have a pending transaction (Invoice ID: {$this->id}, status: {$this->status}");
        }

        \DB::transaction(function () use ($error) {
            // fulfill invoice
            $this->payFailed($error);
        });
    }

    /**
     * Pay invoice.
     *
     * @return void
     */
    public function paySuccess()
    {
        \DB::transaction(function () {
            // set invoice status as paid
            $this->setPaid();

            // set transaction as success
            // Important: according to current design, the rule is: one invoice only has one pending transaction
            $this->getPendingTransaction()->setSuccess();

            // set subscription status
            $this->process();
        });
    }

    /**
     * Pay invoice failed.
     *
     * @return void
     */
    public function payFailed($error)
    {
        $this->getPendingTransaction()->setFailed(trans('messages.payment.cannot_charge', [
            'id' => $this->uid,
            'error' => $error,
        ]));

        // event after paying failed
        $this->checkoutAfterPayFailed($error);
    }

    /**
     * Process invoice.
     *
     * @return void
     */
    public function process()
    {
        throw new \Exception('Need sub classes implement this function! Not call directly from this parent class!');
    }

    /**
     * Check paid.
     *
     * @return void
     */
    public function isPaid()
    {
        return $this->status == self::STATUS_PAID;
    }

    /**
     * Check done.
     *
     * @return void
     */
    public function isDone()
    {
        return $this->status == self::STATUS_DONE;
    }

    /**
     * Check rejected.
     *
     * @return void
     */
    public function isRejected()
    {
        return $this->status == self::STATUS_REJECTED;
    }

    /**
     * Get billing info.
     *
     * @return void
     */
    public function getBillingInfoBase($chargeInfo, $plan = null)
    {
        return  [
            'title' => $this->title,
            'description' => $this->description,
            'bill' => $this->invoiceItems()->get()->map(function ($item) {
                return [
                    'title' => $item->title,
                    'description' => $item->description,
                    'price' => format_price($item->amount, $item->invoice->currency->format),
                    'tax' => format_price($item->getTax(), $item->invoice->currency->format),
                    'tax_p' => number_with_delimiter($item->getTaxPercent()),
                    'discount' => format_price($item->discount, $item->invoice->currency->format),
                    'sub_total' => format_price($item->subTotal(), $item->invoice->currency->format),
                ];
            }),
            'charge_info' => $chargeInfo,
            'total' => format_price($this->total(), $this->currency->format),
            'sub_total' => format_price($this->subTotal(), $this->currency->format),
            'tax' => format_price($this->getTax(), $this->currency->format),
            'pending' => $this->getPendingTransaction(),
            'invoice_uid' => $this->uid,
            'due_date' => $this->created_at,
            'type' => $this->type,
            'plan' => $plan ?? null,
            'has_fee' => $this->fee ? $this->fee > 0 : false,
            'fee' => $this->fee ? format_price($this->fee, $this->currency->format) : null,
            'billing_display_name' => (
                get_localization_config('show_last_name_first', $this->customer->getLanguageCode()) ?
                    ($this->billing_last_name . ' ' . $this->billing_first_name) :
                    ($this->billing_first_name . ' ' . $this->billing_last_name)
            ),
            'billing_first_name' => $this->billing_first_name,
            'billing_last_name' => $this->billing_last_name,
            'billing_address' => $this->billing_address,
            'billing_country' => $this->billing_country_id ? Country::find($this->billing_country_id)->name : '',
            'billing_email' => $this->billing_email,
            'billing_phone' => $this->billing_phone,
        ];
    }

    /**
     * Add transaction.
     *
     * @return array
     */
    public function createPendingTransaction($gateway)
    {
        if ($this->getPendingTransaction()) {
            throw new \Exception('Invoice already has a pending transaction!');
        }

        // @todo: dung transactions()->new....
        $transaction = new Transaction();
        $transaction->invoice_id = $this->id;
        $transaction->status = Transaction::STATUS_PENDING;
        $transaction->allow_manual_review = false;

        // This information is needed for verifying a transaction status later on
        // Sometimes method is not needed. For example, when upgrading to a ZERO priced plan, the checkout
        if (!is_null($gateway)) {
            $transaction->allow_manual_review = $gateway->allowManualReviewingOfTransaction();
            $transaction->method = $gateway->getType();
        }

        $transaction->save();

        return $transaction;
    }

    public function isUnpaid()
    {
        return in_array($this->status, [
            self::STATUS_NEW,
        ]);
    }

    public function checkoutAfterPayFailed($error)
    {
        throw new \Exception('Need sub classes implement this function! Not call directly from this parent class!');
    }

    /**
     * Checkout.
     *
     * @return array
     */
    public function checkout(PaymentGatewayInterface $gateway = null, Closure $executePayment)
    {
        \DB::transaction(function () use ($gateway, $executePayment) {
            // Create pending transaction, do not include it in a DB::transaction
            // The pending transaction will turn into FAILED or DONE
            // It only keeps pending for Offline payment
            $this->createPendingTransaction($gateway);

            try {
                $result = $executePayment($this);
            } catch (\Throwable $e) {
                // Payment failed, due to an unknown error
                $this->payFailed("PAYMENT FAILED: ".$e->getMessage());

                // Immediately return
                return;
            }

            // invoice handle result
            $this->handleTransactionResult($result);
        });
    }

    public function isFree()
    {
        return $this->total() == 0;
    }

    public function cancel()
    {
        \DB::transaction(function () {
            // Tuỳ từng loại invoice mà xử lý trước khi delete invoice
            $this->beforeCancel();

            // Hiện tại cancel đồng nghĩa với xoá luôn invoice đó
            $this->delete();
        });
    }

    public function beforeCancel()
    {
        throw new \Exception('Need sub classes implement this function! Not call directly from this parent class!');
    }

    public function updateBillingInformation($params)
    {
        $this->fill($params);

        $validator = \Validator::make($params, [
            'billing_first_name' => 'required',
            'billing_last_name' => 'required',
            'billing_address' => 'required',
            'billing_country_id' => 'required',
            'billing_email' => 'required|email',
            'billing_phone' => 'required',
        ]);

        if ($validator->fails()) {
            return $validator;
        }

        $this->save();

        return $validator;
    }

    public function fillBillingAddressNotReplace($billingAddress)
    {
        if (!$this->billing_first_name) {
            $this->billing_first_name = $billingAddress->first_name;
        }
        if (!$this->billing_last_name) {
            $this->billing_last_name = $billingAddress->last_name;
        }
        if (!$this->billing_address) {
            $this->billing_address = $billingAddress->address;
        }
        if (!$this->billing_email) {
            $this->billing_email = $billingAddress->email;
        }
        if (!$this->billing_phone) {
            $this->billing_phone = $billingAddress->phone;
        }
        if (!$this->billing_country_id) {
            $this->billing_country_id = $billingAddress->country_id;
        }
    }

    public function getBillingName()
    {
        $lastNameFirst = get_localization_config('show_last_name_first', $this->customer->getLanguageCode());

        if ($lastNameFirst) {
            return htmlspecialchars(trim($this->billing_last_name . ' ' . $this->billing_first_name));
        } else {
            return htmlspecialchars(trim($this->billing_first_name . ' ' . $this->billing_last_name));
        }
    }

    public function getTaxPercent()
    {
        if ($this->billing_country_id) {
            $country = Country::find($this->billing_country_id);
            $tax = Setting::getTaxByCountry($country);
        } else {
            $tax = Setting::getTaxByCountry(null);
        }

        return $tax;
    }

    public static function createInvoice($type, $title, $description, $customer_id, $currency_id, $billing_address, $invoiceItems)
    {
        // create invoice
        $invoice = new self();
        $invoice->status = self::STATUS_NEW;
        $invoice->type = $type;

        $invoice->title = $title;
        $invoice->description = $description;
        $invoice->customer_id = $customer_id;
        $invoice->currency_id = $currency_id;

        // fill billing information
        if ($billing_address) {
            $invoice->billing_first_name = $billing_address->first_name;
            $invoice->billing_last_name = $billing_address->last_name;
            $invoice->billing_address = $billing_address->address;
            $invoice->billing_email = $billing_address->email;
            $invoice->billing_phone = $billing_address->phone;
            $invoice->billing_country_id = $billing_address->country_id;
        }

        // save
        $invoice->save();

        // add invoice number
        $invoice->createInvoiceNumber();

        // add item
        foreach ($invoiceItems as $invoiceItem) {
            $invoiceItem->invoice_id = $invoice->id;

            // fixed tax
            $invoiceItem->tax = $invoice->getTaxPercent();

            $invoiceItem->save();
        }

        return $invoice;
    }

    public function hasBillingInformation()
    {
        if (empty($this->billing_first_name) ||
            empty($this->billing_last_name) ||
            empty($this->billing_phone) ||
            empty($this->billing_address) ||
            empty($this->billing_country_id) ||
            empty($this->billing_email)
        ) {
            return false;
        }

        return true;
    }

    public static function getTemplateContent($languageCode='en')
    {
        if (Setting::get('invoice.custom_template')) {
            return Setting::get('invoice.custom_template');
        } else {
            return view('invoices.template', [
                'languageCode' => $languageCode,
            ]);
        }
    }

    public function getInvoiceHtml()
    {
        $content = self::getTemplateContent($this->customer->getLanguageCode());
        $bill = $this->getBillingInfo();

        // transalte tags
        $values = [
            ['tag' => '{COMPANY_NAME}', 'value' => Setting::get('company_name')],
            ['tag' => '{COMPANY_ADDRESS}', 'value' => Setting::get('company_address')],
            ['tag' => '{COMPANY_EMAIL}', 'value' => Setting::get('company_email')],
            ['tag' => '{COMPANY_PHONE}', 'value' => Setting::get('company_phone')],
            ['tag' => '{FIRST_NAME}', 'value' => $bill['billing_first_name']],
            ['tag' => '{LAST_NAME}', 'value' => $bill['billing_last_name']],
            ['tag' => '{ADDRESS}', 'value' => $bill['billing_address']],
            ['tag' => '{COUNTRY}', 'value' => $bill['billing_country']],
            ['tag' => '{EMAIL}', 'value' => $bill['billing_email']],
            ['tag' => '{PHONE}', 'value' => $bill['billing_phone']],
            ['tag' => '{INVOICE_NUMBER}', 'value' => $this->number],
            ['tag' => '{CURRENT_DATETIME}', 'value' => $this->customer->formatCurrentDateTime('datetime_full')],
            ['tag' => '{INVOICE_DUE_DATE}', 'value' => $this->customer->formatDateTime($bill['due_date'], 'datetime_full')],
            ['tag' => '{ITEMS}', 'value' => view('invoices._template_items', [
                'bill' => $bill,
                'invoice' => $this,
            ])],
        ];

        foreach ($values as $value) {
            $content = str_replace($value['tag'], $value['value'], $content);
        }

        $content = StringHelper::transformUrls($content, function ($url, $element) {
            if (strpos($url, '#') === 0) {
                return $url;
            }

            if (strpos($url, 'mailto:') === 0) {
                return $url;
            }

            if (parse_url($url, PHP_URL_HOST) === false) {
                // false ==> if url is invalid
                // null ==> if url does not have host information
                return $url;
            }

            if (StringHelper::isTag($url)) {
                return $url;
            }

            if (strpos($url, '/') === 0) {
                // absolute url with leading slash (/) like "/hello/world"

                return join_url(getAppHost(), $url);
            } elseif (strpos($url, 'data:') === 0) {
                // base64 image. Like: "data:image/png;base64,iVBOR"
                return $url;
            } else {
                return $url;
            }
        });

        return $content;
    }

    public function exportToPdf()
    {
        // instantiate and use the dompdf class
        $dompdf = new Dompdf(array('enable_remote' => true));
        $content = mb_convert_encoding($this->getInvoiceHtml(), 'HTML-ENTITIES', 'UTF-8');
        $dompdf->loadHtml($content);

        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4');

        // Render the HTML as PDF
        $dompdf->render();

        return $dompdf->output();
    }

    public static function getTags()
    {
        $tags = [
            ['name' => '{COMPANY_NAME}', 'required' => false],
            ['name' => '{COMPANY_ADDRESS}', 'required' => false],
            ['name' => '{COMPANY_EMAIL}', 'required' => false],
            ['name' => '{COMPANY_PHONE}', 'required' => false],
            ['name' => '{FIRST_NAME}', 'required' => false],
            ['name' => '{LAST_NAME}', 'required' => false],
            ['name' => '{ADDRESS}', 'required' => false],
            ['name' => '{COUNTRY}', 'required' => false],
            ['name' => '{EMAIL}', 'required' => false],
            ['name' => '{PHONE}', 'required' => false],
            ['name' => '{INVOICE_NUMBER}', 'required' => false],
            ['name' => '{CURRENT_DATETIME}', 'required' => false],
            ['name' => '{INVOICE_DUE_DATE}', 'required' => false],
            ['name' => '{ITEMS}', 'required' => false],
            ['name' => '{CUSTOMER_ADDRESS}', 'required' => false],
        ];

        return $tags;
    }

    public function createInvoiceNumber()
    {
        if (Setting::get('invoice.current')) {
            $currentNumber = intval(Setting::get('invoice.current'));
        } else {
            $currentNumber = 1;
        }

        $this->number = sprintf(Setting::get('invoice.format'), $currentNumber);
        $this->save();

        // update current number
        Setting::set('invoice.current', ($currentNumber + 1));
    }

    public function setFee($amount)
    {
        $this->fee = $amount;
        $this->save();
    }

    public function updatePaymentServiceFee($gateway)
    {
        // Nếu subTotal của invoice = 0 thì lấy cái minimum amount từ service. Ví dụ Stripe cần 1 số tiền tối thiểu để lưa card
        if ($this->subTotal() == 0) {
            $this->setFee($gateway->getMinimumChargeAmount($this->currency->code));
        }
    }

    public function getCurrencyCode()
    {
        return $this->currency->code;
    }

    public function getBillingCountryCode()
    {
        return ($this->billingCountry ? $this->billingCountry->code : '');
    }

    public function getItsOnlySuccessTransaction()
    {
        $query = $this->transactions()
            ->where('status', Transaction::STATUS_PENDING)
            ->orderBy('created_at', 'desc');

        if ($query->count()) {
            throw new \Exception("The invoice {$this->uid} should have only one success transaction!");
        }

        return $query->first();
    }

    public function getTransactions()
    {
        return $this->transactions()
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public static function getInvoiceTypeSelectOptions()
    {
        return [
            ['text' => trans('messages.invoice.type.' . InvoiceTopUp::TYPE_TOP_UP), 'value' => InvoiceTopUp::TYPE_TOP_UP],
            ['text' => trans('messages.invoice.type.' . InvoiceNewSubscription::TYPE_NEW_SUBSCRIPTION), 'value' => InvoiceNewSubscription::TYPE_NEW_SUBSCRIPTION],
            ['text' => trans('messages.invoice.type.' . InvoiceRenewSubscription::TYPE_RENEW_SUBSCRIPTION), 'value' => InvoiceRenewSubscription::TYPE_RENEW_SUBSCRIPTION],
            ['text' => trans('messages.invoice.type.' . InvoiceChangePlan::TYPE_CHANGE_PLAN), 'value' => InvoiceChangePlan::TYPE_CHANGE_PLAN],
        ];
    }

    public function refreshPrice()
    {
        throw new \Exception('Need sub classes implement this function! Not call directly from this parent class!');
    }

    public function mapType()
    {
        //
        switch ($this->type) {
            case InvoiceNewSubscription::TYPE_NEW_SUBSCRIPTION:
                return InvoiceNewSubscription::find($this->id);
                break;
            case InvoiceRenewSubscription::TYPE_RENEW_SUBSCRIPTION:
                return InvoiceRenewSubscription::find($this->id);
                break;
            case InvoiceChangePlan::TYPE_CHANGE_PLAN:
                return InvoiceChangePlan::find($this->id);
                break;
            case InvoiceTopUp::TYPE_TOP_UP:
                return InvoiceTopUp::find($this->id);
                break;
            default:
                throw new \Exception("Invoice type #{$this->type} not found!");
        }
    }

    public function handleTransactionResult($result)
    {
        try {
            if ($result->isDone()) {
                // @todo Log something here: payment already done
                // So, if an error occurs, it is likely due to following code

                // Stripe, PayPal, Braintree for example
                $this->paySuccess();
            } elseif ($result->isFailed()) {
                // Payment failed as reported by Stripe
                $this->payFailed($result->error);
            } elseif ($result->isPending()) {
                // Coin, offline shouls return this status
                // Wait more, check again later....
                // Coinpayment, offline
                // logging by type
            }
        } catch (\Throwable $e) {
            // Payment failed, due to an unknown error
            $this->payFailed("Payment done, but something went wrong after that: ".$e->getMessage());
        }
    }

    public function formattedTotal()
    {
        return format_price($this->total(), $this->currency->format);
    }
}
