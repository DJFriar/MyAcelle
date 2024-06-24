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

use Validator;
use Acelle\Model\SendingServer;
use Acelle\Library\QuotaManager;
use Acelle\Library\Contracts\PlanInterface;

class PlanGeneral extends Plan implements PlanInterface
{
    protected $table = 'plans';

    // General type
    public const TYPE_GENERAL = 'general';

    // Sending server options
    public const SENDING_SERVER_OPTION_SYSTEM = 'system';
    public const SENDING_SERVER_OPTION_OWN = 'own';
    public const SENDING_SERVER_OPTION_SUBACCOUNT = 'subaccount';

    //
    public const UNLIMITED_PLAN_ID = '9999';

    // General plans
    public static function scopeGeneral($query)
    {
        $query = $query->where('TYPE', self::TYPE_GENERAL);
    }

    public static function newGeneral()
    {
        $plan = self::newDefaultPlan();
        $plan->type = self::TYPE_GENERAL;
        return $plan;
    }

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
        'paddle_plan_id',
        'admin_id',
        'trial_amount',
        'trial_unit',
        'own_tracking_domain_required',
    ];

    /**
     * The rules for validation.
     *
     * @var array
     */
    public function rules()
    {
        $rules = parent::rules();

        $options = self::defaultOptions();
        foreach ($options as $type => $option) {
            if ($type != 'sending_server_subaccount_uid' && $type != 'email_footer_enabled' && $type != 'email_footer_trial_period_only' && $type != 'html_footer' && $type != 'plain_text_footer') {
                $rules['options.'.$type] = 'required';
            }
        }

        if ($this->getOption('sending_server_option') == \Acelle\Model\PlanGeneral::SENDING_SERVER_OPTION_SUBACCOUNT) {
            $rules['options.sending_server_subaccount_uid'] = 'required';
        }

        return $rules;
    }

    /**
     * The rules for validation.
     *
     * @var array
     */
    public function validationRules()
    {
        $rules = [
            'general' => [
                'plan.general.name' => 'required',
                'plan.general.currency_id' => 'required',
                'plan.general.frequency_amount' => 'sometimes|required|min:1',
                'plan.general.frequency_unit' => 'sometimes|required',
                'plan.general.price' => 'required|min:0',
            ],
            'options' => [],
        ];

        $options = self::defaultOptions();
        foreach ($options as $type => $option) {
            if ($type != 'sending_server_subaccount_uid' && !in_array($type, ['plain_text_footer', 'html_footer', 'bounce_rate_theshold'])) {
                $rules['options']['plan.options.'.$type] = 'sometimes|required';
            }
        }

        if ($this->getOption('sending_server_option') == \Acelle\Model\PlanGeneral::SENDING_SERVER_OPTION_SUBACCOUNT) {
            $rules['options']['plan.options.sending_server_subaccount_uid'] = 'sometimes|required';
        }

        return $rules;
    }

    /**
     * The rules for validation.
     *
     * @var array
     */
    public function generalRules()
    {
        $rules = array(
            'name' => 'required',
            'currency_id' => 'required',
            'frequency_amount' => 'required|min:1',
            'frequency_unit' => 'required',
            'price' => 'required|min:0',
            'trial_amount' => 'required|min:0',
            'trial_unit' => 'required',
        );

        return $rules;
    }

    /**
     * The rules for validation.
     *
     * @var array
     */
    public function resourcesRules()
    {
        $rules = array(
            'options.email_max' => 'required',
            'options.list_max' => 'required',
            'options.subscriber_max' => 'required',
            'options.subscriber_per_list_max' => 'required',
            'options.segment_per_list_max' => 'required',
            'options.campaign_max' => 'required',
            'options.automation_max' => 'required',
            'options.max_process' => 'required',
            'options.max_size_upload_total' => 'required',
            'options.max_file_size_upload' => 'required',
        );

        return $rules;
    }

    /**
     * The rules for validation.
     *
     * @var array
     */
    public function sendingLimitRules()
    {
        $rules = array(
            'options.sending_limit' => 'required',
            'options.sending_quota' => 'required',
            'options.sending_quota_time' => 'required',
            'options.sending_quota_time_unit' => 'required',
        );

        return $rules;
    }

    /**
     * The rules for validation.
     *
     * @var array
     */
    public function apiRules()
    {
        $rules = array(
            'name' => 'required',
            'currency_id' => 'required',
            'frequency_amount' => 'required|min:1',
            'frequency_unit' => 'required',
            'price' => 'required|min:0',
        );

        if ($this->getOption('sending_server_option') == \Acelle\Model\PlanGeneral::SENDING_SERVER_OPTION_SUBACCOUNT) {
            $rules['options.sending_server_subaccount_uid'] = 'required';
        }

        return $rules;
    }

    public function plansSendingServers()
    {
        return $this->hasMany('Acelle\Model\PlansSendingServer', 'plan_id');
    }

    public function plansEmailVerificationServers()
    {
        return $this->hasMany('Acelle\Model\PlansEmailVerificationServer', 'plan_id');
    }

    /**
     * Items per page.
     *
     * @var array
     */
    public static $itemsPerPage = 25;

    /**
     * Filter items.
     *
     * @return collect
     */
    public static function scopeFilter($query, $request)
    {
        $query = $query->select('plans.*');

        // filters
        $filters = $request->all();

        if (!empty($request->admin_id)) {
            $query = $query->where('plans.admin_id', '=', $request->admin_id);
        }
    }

    /**
     * Search items.
     *
     * @return collect
     */
    public static function scopeSearch($query, $keyword)
    {
        $query = $query->general();

        // Keyword
        if (!empty(trim($keyword))) {
            foreach (explode(' ', trim($keyword)) as $keyword) {
                $query = $query->where(function ($q) use ($keyword) {
                    $q->orwhere('plans.name', 'like', '%'.$keyword.'%');
                });
            }
        }
    }

    public static function bounceRateThesholdOptions()
    {
        $options = [
            ['value' => '', 'text' => trans('messages.none')],
        ];
        for ($i = 1; $i <= 100; $i++) {
            $options[] = ['value' => $i, 'text' => $i . '%'];
        }

        return $options;
    }

    /**
     * Get sending limit types.
     *
     * @return array
     */
    public static function sendingLimitValues()
    {
        return [
            'unlimited' => [
                'quota_value' => -1,
                'quota_base' => -1,
                'quota_unit' => 'day',
            ],
            '100_per_minute' => [
                'quota_value' => 100,
                'quota_base' => 1,
                'quota_unit' => 'minute',
            ],
            '1000_per_hour' => [
                'quota_value' => 1000,
                'quota_base' => 1,
                'quota_unit' => 'hour',
            ],
            '10000_per_hour' => [
                'quota_value' => 10000,
                'quota_base' => 1,
                'quota_unit' => 'hour',
            ],
            '50000_per_hour' => [
                'quota_value' => 50000,
                'quota_base' => 1,
                'quota_unit' => 'hour',
            ],
            '10000_per_day' => [
                'quota_value' => 10000,
                'quota_base' => 1,
                'quota_unit' => 'day',
            ],
            '100000_per_day' => [
                'quota_value' => 100000,
                'quota_base' => 1,
                'quota_unit' => 'day',
            ],
        ];
    }

    /**
     * Default options for new plan.
     *
     * @return array
     */
    public static function defaultOptions()
    {
        $options = array_merge([], [
            'email_max' => '-1',
            'list_max' => '-1',
            'subscriber_max' => '-1',
            'subscriber_per_list_max' => '-1',
            'segment_per_list_max' => '-1',
            'campaign_max' => '-1',
            'automation_max' => '-1',
            'sending_limit' => 'unlimited',
            'sending_quota' => '-1',
            'sending_quota_time' => '-1',
            'sending_quota_time_unit' => 'day',
            'max_process' => '1',
            'max_size_upload_total' => '500',
            'max_file_size_upload' => '5',
            'unsubscribe_url_required' => 'no',
            'access_when_offline' => 'no',
            //'create_sending_servers' => 'no',
            'create_sending_domains' => 'yes',
            'sending_servers_max' => '-1',
            'sending_domains_max' => '-1',
            'all_email_verification_servers' => 'yes',
            'create_email_verification_servers' => 'no',
            'verification_credits_limit' => '10000',
            'email_verification_servers_max' => '-1',
            'list_import' => 'yes',
            'list_export' => 'yes',
            'all_sending_server_types' => 'yes',
            'sending_server_types' => [],
            'sending_server_option' => self::SENDING_SERVER_OPTION_SYSTEM,
            'sending_server_subaccount_uid' => null,
            'api_access' => 'yes',
            'email_footer_enabled' => 'no',
            'email_footer_trial_period_only' => 'no',
            'html_footer' => '',
            'plain_text_footer' => '',
            'payment_gateway' => '',
            'bounce_rate_theshold' => '',
            'billing_cycle' => 'monthly',
        ]);

        // Sending server types
        foreach (\Acelle\Model\SendingServer::types() as $key => $type) {
            $options['sending_server_types'][$key] = 'yes';
        }

        return $options;
    }

    /**
     * Get options.
     *
     * @return array
     */
    public function getOptions()
    {
        if (empty($this->options)) {
            return self::defaultOptions();
        } else {
            $defaul_options = self::defaultOptions();
            $saved_options = json_decode($this->options, true);
            foreach ($defaul_options as $x => $group) {
                if (isset($saved_options[$x])) {
                    $defaul_options[$x] = $saved_options[$x];
                }
            }

            return $defaul_options;
        }
    }

    /**
     * Get option.
     *
     * @return string
     */
    public function getOption($name)
    {
        return $this->getOptions()[$name];
    }

    /**
     * Set option.
     */
    public function setOption($name, $value)
    {
        $options = json_decode($this->options, true);
        $options[$name] = $value;

        $this->options = json_encode($options);
        $this->save();
    }

    /**
     * Set option.
     */
    public function asignOption($name, $value)
    {
        $options = json_decode($this->options, true);
        $options[$name] = $value;

        $this->options = json_encode($options);
    }

    /**
     * Fill option from request.
     */
    public function fillOptions($options = [])
    {
        $defaultOptions = self::defaultOptions();
        $saveOptions = $this->options ? array_merge($defaultOptions, json_decode($this->options, true)) : $defaultOptions;
        if (!empty($options)) {
            foreach ($options as $key => $value) {
                $saveOptions[$key] = $value;
            }
        }

        // workaround
        if (empty($this->description)) {
            $this->description = '[ プランの説明はこちら ]';
        }

        $this->options = json_encode($saveOptions);
    }

    /**
     * Update sending servers.
     *
     * @return array
     */
    public function updateSendingServers($servers)
    {
        $this->plansSendingServers()->delete();
        foreach ($servers as $key => $param) {
            if ($param['check']) {
                $server = SendingServer::findByUid($key);
                $row = new PlansSendingServer();
                $row->plan_id = $this->id;
                $row->sending_server_id = $server->id;
                $row->fitness = $param['fitness'];
                $row->save();
            }
        }
    }

    /**
     * Multi process select options.
     *
     * @return array
     */
    public static function multiProcessSelectOptions()
    {
        $options = [['value' => 1, 'text' => trans('messages.one_single_process')]];
        for ($i = 2; $i < 4; ++$i) {
            $options[] = ['value' => $i, 'text' => $i];
        }

        return $options;
    }

    /**
     * Display group quota.
     *
     * @return array
     */
    public function displayQuota()
    {
        if ($this->getOption('sending_quota') == -1) {
            return trans('messages.unlimited');
        } elseif ($this->getOption('sending_quota_time') == -1) {
            return $this->getOption('sending_quota');
        } else {
            return strtolower(number_with_delimiter($this->getOption('sending_quota')).' '.trans('messages.'.\Acelle\Library\Tool::getPluralPrase('email', $this->getOption('sending_quota'))).' / '.$this->getOption('sending_quota_time').' '.trans('messages.'.\Acelle\Library\Tool::getPluralPrase($this->getOption('sending_quota_time_unit'), $this->getOption('sending_quota'))));
        }
    }

    /**
     * Display plan price.
     *
     * @return array
     */
    public function displayPrice()
    {
        return format_price($this->price, $this->currency->format);
    }

    /**
     * Display total quota.
     *
     * @return array
     */
    public function displayTotalQuota()
    {
        if ($this->getOption('email_max') == -1) {
            return trans('messages.unlimited');
        } else {
            return number_with_delimiter($this->getOption('email_max'));
        }
    }

    /**
     * Display max lists.
     *
     * @return array
     */
    public function displayMaxList()
    {
        if ($this->getOption('list_max') == -1) {
            return trans('messages.unlimited');
        } else {
            return number_with_delimiter($this->getOption('list_max'));
        }
    }

    /**
     * Display max subscribers.
     *
     * @return array
     */
    public function displayMaxSubscriber()
    {
        if ($this->getOption('subscriber_max') == -1) {
            return trans('messages.unlimited');
        } else {
            return number_with_delimiter($this->getOption('subscriber_max'));
        }
    }

    /**
     * Display max campaign.
     *
     * @return array
     */
    public function displayMaxCampaign()
    {
        if ($this->getOption('campaign_max') == -1) {
            return trans('messages.unlimited');
        } else {
            return number_with_delimiter($this->getOption('campaign_max'));
        }
    }

    /**
     * Display max campaign.
     *
     * @return array
     */
    public function displayMaxSizeUploadTotal()
    {
        if ($this->getOption('max_size_upload_total') == -1) {
            return trans('messages.unlimited');
        } else {
            return number_with_delimiter($this->getOption('max_size_upload_total'));
        }
    }

    /**
     * Display max campaign.
     *
     * @return array
     */
    public function displayFileSizeUpload()
    {
        if ($this->getOption('max_file_size_upload') == -1) {
            return trans('messages.unlimited');
        } else {
            return number_with_delimiter($this->getOption('max_file_size_upload'));
        }
    }

    /**
     * Display sending ervers permission.
     *
     * @return array
     */
    public function displayAllowCreateSendingServer()
    {
        if ($this->getOption('sending_server_option') != \Acelle\Model\PlanGeneral::SENDING_SERVER_OPTION_OWN) {
            return trans('messages.feature_not_allow');
        }

        if ($this->getOption('sending_servers_max') == -1) {
            return trans('messages.unlimited');
        } else {
            return $this->getOption('sending_servers_max');
        }
    }

    /**
     * Display sending domains permission.
     *
     * @return array
     */
    public function displayAllowCreateSendingDomain()
    {
        if ($this->getOption('create_sending_domains') == 'no') {
            return trans('messages.feature_not_allow');
        }

        if ($this->getOption('sending_domains_max') == -1) {
            return trans('messages.unlimited');
        } else {
            return $this->getOption('sending_domains_max');
        }
    }

    /**
     * Frequency time unit options.
     *
     * @return array
     */
    public static function quotaTimeUnitOptions()
    {
        return [
            ['value' => 'minute', 'text' => trans('messages.minute')],
            ['value' => 'hour', 'text' => trans('messages.hour')],
            ['value' => 'day', 'text' => trans('messages.day')],
        ];
    }

    /**
     * Fill email verification servers.
     */
    public function fillPlansEmailVerificationServers($params)
    {
        $this->plansEmailVerificationServers = collect([]);
        foreach ($params as $key => $param) {
            if ($param['check']) {
                $server = \Acelle\Model\EmailVerificationServer::findByUid($key);
                $row = new \Acelle\Model\PlansEmailVerificationServer();
                $row->plan_id = $this->id;
                $row->server_id = $server->id;
                $this->plansEmailVerificationServers->push($row);
            }
        }
    }

    /**
     * Update email verification servers.
     *
     * @return array
     */
    public function updateEmailVerificationServers($servers)
    {
        $this->plansEmailVerificationServers()->delete();
        foreach ($servers as $key => $param) {
            if ($param['check']) {
                $server = \Acelle\Model\EmailVerificationServer::findByUid($key);
                $row = new \Acelle\Model\PlansEmailVerificationServer();
                $row->plan_id = $this->id;
                $row->server_id = $server->id;
                $row->save();
            }
        }
    }

    /**
     * Display sending ervers permission.
     *
     * @return array
     */
    public function displayAllowCreateEmailVerificationServer()
    {
        if ($this->getOption('create_email_verification_servers') == 'no') {
            return trans('messages.feature_not_allow');
        }

        if ($this->getOption('email_verification_servers_max') == -1) {
            return trans('messages.unlimited');
        } else {
            return $this->getOption('email_verification_servers_max');
        }
    }

    /**
     * Fill from request.
     */
    public function fillAll($request)
    {
        // if has general params
        if (isset($request->plan['general'])) {
            $params = $request->plan['general'];
            if ($params['frequency_amount'] == '') {
                $params['frequency_amount'] = 1;
            }
            $this->fill($params);
        }

        // if has options params
        if (isset($request->plan['options'])) {
            $this->fillOptions($request->plan['options']);
        }

        //// if has email verifications
        //if (isset($request->plan['email_verification_servers'])) {
        //    $this->fillPlansEmailVerificationServers($request->plan['email_verification_servers']);
        //}

        // old request
        if ($request->old()) {
            if (isset($request->old()['plan']['general'])) {
                // fill all attributes
                $this->fill($request->old()['plan']['general']);
            }
            if (isset($request->old()['plan']['options'])) {
                // fill all options
                $this->fillOptions($request->old()['plan']['options']);
            }
            //if (isset($request->old()['plan']['email_verification_servers'])) {
            //    $this->fillPlansEmailVerificationServers($request->old()['plan']['email_verification_servers']);
            //}
        }

        // billing cycle
        $billingCycle = $this->getOption('billing_cycle');
        if ($billingCycle && isset($billingCycle) && $billingCycle != 'custom' && $billingCycle != 'other') {
            $limits = self::billingCycleValues()[$billingCycle];
            $this->frequency_amount = $limits['frequency_amount'];
            $this->frequency_unit = $limits['frequency_unit'];
        }

        // billing cycle
        $sendingLimit = $this->getOption('sending_limit');
        if (isset($sendingLimit) && $sendingLimit != 'custom' && $sendingLimit != 'other') {
            $limits = self::sendingLimitValues()[$sendingLimit];
            $this->asignOption('sending_quota', $limits['quota_value']);
            $this->asignOption('sending_quota_time', $limits['quota_base']);
            $this->asignOption('sending_quota_time_unit', $limits['quota_unit']);
        }
    }

    /**
     * Fill from request.
     */
    public function validate($request)
    {
        $rules = [];
        foreach (array_keys($request->plan) as $key) {
            if (isset($this->validationRules()[$key])) {
                $rules = array_merge($rules, $this->validationRules()[$key]);
            }
        }

        return Validator::make($request->all(), $rules);
    }

    /**
     * Fill from request.
     */
    public function saveAll($request)
    {
        $this->fillAll($request);

        $validator = $this->validate($request);

        if ($validator->fails()) {
            return $validator;
        }

        $this->save();

        // For email verification servers
        if (isset($request->plan['email_verification_servers'])) {
            $this->updateEmailVerificationServers($request->plan['email_verification_servers']);
        }

        return $validator;
    }

    /**
     ** Add sending server by uid.
     **/
    public function addSendingServerByUid($sendinServerUid)
    {
        $server = SendingServer::findByUid($sendinServerUid);
        $row = new PlansSendingServer();
        $row->plan_id = $this->id;
        $row->sending_server_id = $server->id;
        $row->fitness = '50';

        // First primary
        if (!$this->plansSendingServers()->where('is_primary', '=', true)->count()) {
            $row->is_primary = true;
        }

        $row->save();
    }

    /**
     ** Remove sending server by uid.
     **/
    public function removeSendingServerByUid($sendinServerUid)
    {
        $server = SendingServer::findByUid($sendinServerUid);
        $this->plansSendingServers()->where('sending_server_id', '=', $server->id)->delete();

        // First primary
        $query = $this->plansSendingServers()->where('is_primary', '=', true);
        if (!$query->count()) {
            $first = $this->plansSendingServers()->first();
            if ($first) {
                $first->is_primary = true;
                $first->save();
            }
        }
    }

    /**
     ** Remove sending server by uid.
     **/
    public function setPrimarySendingServer($sendinServerUid)
    {
        $this->plansSendingServers()->update(['is_primary' => false]);

        $server = SendingServer::findByUid($sendinServerUid);
        $this->plansSendingServers()->where('sending_server_id', '=', $server->id)->update(['is_primary' => true]);
    }

    /**
     ** Update fitness by sending servers.
     **/
    public function updateFitnesses($hash)
    {
        foreach ($hash as $uid => $value) {
            $sendingServer = SendingServer::findByUid($uid);
            \Acelle\Model\PlansSendingServer::where('sending_server_id', '=', $sendingServer->id)
                ->update(['fitness' => $value]);
        }
    }

    /**
     ** Get Primary sending server.
     **/
    public function primarySendingServer()
    {
        if (!$this->useSystemSendingServer()) {
            throw new \Exception('ACELLE ERROR: 120000700392');
        }

        $pss = $this->plansSendingServers()->where('is_primary', '=', true)->first();
        // @todo: raise 1 cái exception nếu $pss null, ko return null sẽ gây lỗi tiềm ẩn
        return $pss ? $pss->sendingServer->mapType() : null;
    }

    /**
     * Get all verified identities.
     *
     * @return array
     */
    public function getVerifiedIdentities()
    {
        $result = [];
        foreach ($this->sendingServers()->get() as $sendingServer) {
            $result = array_merge($result, $sendingServer->mapType()->getVerifiedIdentities());
        }
        return array_unique($result);
    }

    /**
     * Get all .
     *
     * @var bool
     */
    public function activePlansEmailVerificationServers()
    {
        return $this->plansEmailVerificationServers();
    }

    /**
     * Get list of available email verification servers.
     *
     * @var bool
     */
    public function getEmailVerificationServers()
    {
        if ($this->getOption('all_email_verification_servers') == 'yes') {
            $result = \Acelle\Model\EmailVerificationServer::getAllAdminActive()->get()->map(function ($server) {
                return $server;
            });
        } else {
            $result = $this->activePlansEmailVerificationServers()->get()->map(function ($server) {
                return $server->emailVerificationServer;
            });
        }

        return $result;
    }

    /**
     * Check if plan has primary sending server.
     *
     * @var bool
     */
    public function hasPrimarySendingServer()
    {
        return !is_null($this->primarySendingServer());
    }

    /**
     * Check if plan sending server type is system.
     *
     * @var bool
     */
    public function useSystemSendingServer()
    {
        return $this->getOption('sending_server_option') == \Acelle\Model\PlanGeneral::SENDING_SERVER_OPTION_SYSTEM;
    }

    public function useOwnSendingServer()
    {
        return $this->getOption('sending_server_option') == \Acelle\Model\PlanGeneral::SENDING_SERVER_OPTION_OWN;
    }

    /**
     * Get all available plans for customer register.
     *
     * @var bool
     */
    public static function getAvailableGeneralPlans()
    {
        return self::general()->active()->available()->get();
    }

    /**
     * Get all sending servers.
     *
     * @var collect
     */
    public function sendingServers()
    {
        return SendingServer::whereIn('id', $this->plansSendingServers()->pluck('sending_server_id')->toArray());
    }

    public function activeSendingServers()
    {
        // do not care if sending server is enabled or not
        return $this->plansSendingServers()->join('sending_servers', 'sending_servers.id', 'plans_sending_servers.sending_server_id');
    }

    /**
     * Check if plan is valid to active.
     *
     * @var bool
     */
    public function isValid()
    {
        // use system sending server but has no primary sending server
        if ($this->useSystemSendingServer() && !$this->hasPrimarySendingServer()) {
            return false;
        }

        // else return true
        return true;
    }

    /**
     * Copy new plan.
     */
    public function copy($name)
    {
        $copy = $this->replicate(['cache', 'last_error', 'run_at']);
        $copy->uid = uniqid();
        $copy->name = $name;
        $copy->created_at = \Carbon\Carbon::now();
        $copy->updated_at = \Carbon\Carbon::now();
        $copy->status = self::STATUS_ACTIVE;
        $copy->save();

        // check status
        $copy->checkStatus();
    }

    /**
     * Check status of sending server.
     *
     * @var void
     */
    public function checkStatus()
    {
        // disable sending server if it is not valid
        if (!$this->isValid()) {
            $this->disable();

            $this->visibleOff();
        } else {
            $this->enable();
        }
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

    public static function newDefaultPlan()
    {
        $plan = new self([
            'price' => 0,
            'frequency_amount' => 1,
            'frequency_unit' => 'month',
            'trial_amount' => '0',
            'trial_unit' => 'day',
            'vat' => 0
        ]);

        $plan->status = self::STATUS_INACTIVE;
        $plan->type = self::TYPE_GENERAL;

        return $plan;
    }

    public function getQuotaSettings($name): ?array
    {
        $quota = [];
        $options = $this->getOptions();

        // Take limits from sending credits
        $sendingCredits = $options['email_max'];
        if ($sendingCredits != QuotaManager::QUOTA_UNLIMITED) {
            $quota[] = [
                'name' => "Plan's sending limit",
                'period_unit' => $this->getFrequencyUnit(),
                'period_value' => $this->getFrequencyAmount(),
                'limit' => $sendingCredits
            ];
        }

        $timeValue = $options['sending_quota_time'];
        if ($timeValue != QuotaManager::QUOTA_UNLIMITED) {
            $timeUnit = $options['sending_quota_time_unit'];
            $limit = $options['sending_quota'];

            $quota[] = [
                'name' => "Sending limit of {$limit} per {$timeValue} {$timeUnit}",
                'period_unit' => $timeUnit,
                'period_value' => $timeValue,
                'limit' => $limit,
            ];
        }

        return $quota;
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

        //
        \Acelle\Model\Setting::set('terms_of_service.enabled', $params['terms_of_service']['enabled']);
        \Acelle\Model\Setting::set('terms_of_service.content', $params['terms_of_service']['content']);

        return $validator;
    }

    public function isCustomTrackingDomainRequired()
    {
        return $this->own_tracking_domain_required;
    }

    /**
     * Get sending limit select options.
     *
     * @return array
     */
    public function getSendingLimitSelectOptions()
    {
        $options = [];

        foreach (self::sendingLimitValues() as $key => $data) {
            $wording = trans('messages.plan.sending_limit.'.$key);
            $options[] = ['text' => $wording, 'value' => $key];
        }

        // exist
        if ($this->getOption('sending_limit') == 'other') {
            $wording = trans('messages.plan.sending_limit.phrase', [
                'quota_value' => number_with_delimiter($this->getOption('sending_quota'), $precision = 0),
                'quota_base' => number_with_delimiter($this->getOption('sending_quota_time'), $precision = 0),
                'quota_unit' => $this->getOption('sending_quota_time_unit'),
            ]);

            $options[] = ['text' => $wording, 'value' => 'other'];
        }

        // Custom
        $options[] = ['text' => trans('messages.plan.sending_limit.custom'), 'value' => 'custom'];

        return $options;
    }

    public function useOwnEmailVerificationServer()
    {
        // depend on option from active susbcription plan
        return $this->getOption('create_email_verification_servers') == 'yes';
    }
}
