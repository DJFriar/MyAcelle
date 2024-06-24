<?php

namespace Acelle\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Acelle\Http\Controllers\Controller;
use Acelle\Library\UpgradeManager;
use Acelle\Library\ExtendedSwiftMessage;
use Illuminate\Support\Facades\Log;
use Acelle\Model\Setting;
use Acelle\Model\Language;
use Acelle\Model\Template;
use Illuminate\Support\Facades\Session;
use Acelle\Helpers\LicenseHelper;
use App;
use Acelle\Library\Downloader;
use Acelle\Library\Notification\SystemUrl;

class SettingController extends Controller
{
    /**
     * Display and update all settings.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->user()->admin->getPermission('setting_general') == 'yes') {
            return redirect()->action('Admin\SettingController@general');
            //} elseif ($request->user()->admin->getPermission('setting_sending') == 'yes') {
            //   return redirect()->action('Admin\SettingController@sending');
        } elseif ($request->user()->admin->getPermission('setting_system_urls') == 'yes') {
            return redirect()->action('Admin\SettingController@urls');
        } elseif ($request->user()->admin->getPermission('setting_background_job') == 'yes') {
            return redirect()->action('Admin\SettingController@cronjob');
        }
    }

    /**
     * General settings.
     *
     * @return \Illuminate\Http\Response
     */
    public function general(Request $request)
    {
        if ($request->user()->admin->getPermission('setting_general') != 'yes') {
            return $this->notAuthorized();
        }

        // Setting::updateAll();
        $settings = Setting::getAll();
        if (null !== $request->old()) {
            foreach ($request->old() as $name => $value) {
                if (isset($settings[$name])) {
                    $settings[$name]['value'] = $value;
                }
            }
        }

        // validate and save posted data
        if ($request->isMethod('post')) {
            // Prenvent save from demo mod
            if (config('app.demo')) {
                return view('somethingWentWrong', ['message' => trans('messages.operation_not_allowed_in_demo')]);
            }

            $rules = [
                'site_name' => 'required',
                'site_keyword' => 'required',
                'site_online' => 'required',
                'site_offline_message' => 'required',
                'site_description' => 'required',
                'frontend_scheme' => 'required',
                'backend_scheme' => 'required',
                'license' => 'license',
            ];
            $this->validate($request, $rules);

            // Save settings
            foreach ($request->all() as $name => $value) {
                if (in_array($name, [
                    'invoice_current',
                    'invoice_format',
                ])) {
                    $name = str_replace('_', '.', $name);
                }

                if ($name != '_token' && isset($settings[$name])) {
                    // Upload and save image
                    if ($name == 'site_logo_light' || $name == 'site_logo_dark' || $name == 'site_favicon') {
                        if ($request->hasFile($name) && $request->file($name)->isValid()) {
                            Setting::uploadFile($request->file($name), $name, false);
                        }
                    } else {
                        if ($settings[$name]['cat'] == 'general' && $request->user()->admin->getPermission('setting_general') == 'yes') {
                            Setting::set($name, $value);
                        }
                    }
                }
            }

            // Redirect to my lists page
            $request->session()->flash('alert-success', trans('messages.setting.updated'));

            return redirect()->action('Admin\SettingController@general');
        }

        return view('admin.settings.general', [
            'settings' => $settings,
            'action' => 'general',
        ]);
    }

    /**
     * Sending settings.
     *
     * @return \Illuminate\Http\Response
     */
    public function sending(Request $request)
    {
        if ($request->user()->admin->getPermission('setting_sending') != 'yes') {
            return $this->notAuthorized();
        }

        // Setting::updateAll();
        $settings = Setting::getAll();
        if (null !== $request->old()) {
            foreach ($request->old() as $name => $value) {
                if (isset($settings[$name])) {
                    $settings[$name]['value'] = $value;
                }
            }
        }

        // validate and save posted data
        if ($request->isMethod('post')) {
            // Prenvent save from demo mod
            if (config('app.demo')) {
                return view('somethingWentWrong', ['message' => trans('messages.operation_not_allowed_in_demo')]);
            }

            $rules = [
                'sending_campaigns_at_once' => 'required',
                'sending_change_server_time' => 'required',
                'sending_emails_per_minute' => 'required',
                'sending_pause' => 'required',
                'sending_at_once' => 'required',
                'sending_subscribers_at_once' => 'required',
            ];
            $this->validate($request, $rules);

            // Save settings
            foreach ($request->all() as $name => $value) {
                if ($name != '_token' && isset($settings[$name])) {
                    if ($settings[$name]['cat'] == 'sending' && $request->user()->admin->getPermission('setting_sending') == 'yes') {
                        Setting::set($name, $value);
                    }
                }
            }

            // Redirect to my lists page
            $request->session()->flash('alert-success', trans('messages.setting.updated'));

            return redirect()->action('Admin\SettingController@sending');
        }

        return view('admin.settings.sending', [
            'settings' => $settings,
        ]);
    }

    /**
     * Url settings.
     *
     * @return \Illuminate\Http\Response
     */
    public function urls(Request $request)
    {
        if ($request->user()->admin->getPermission('setting_system_urls') != 'yes') {
            return $this->notAuthorized();
        }

        $settings = Setting::getAll();

        // Check URL
        $current = url('/');
        $cached = config('app.url');

        if (!is_null($request->input('debug'))) {
            echo "Current: {$current} vs. Cached: {$cached}";
            return;
        }

        return view('admin.settings.urls', [
            'settings' => $settings,
            'matched' => ($cached == $current),
            'current' => $current,
            'cached' => $cached,
            'action' => 'urls'
        ]);
    }

    /**
     * Cronjob list.
     *
     * @return \Illuminate\Http\Response
     */
    public function cronjob(Request $request)
    {
        if ($request->user()->admin->getPermission('setting_background_job') != 'yes') {
            return $this->notAuthorized();
        }

        $respone = \Acelle\Library\Tool::cronjobUpdateController($request, $this);
        if ($respone == 'done' || $respone['valid'] == true) {
            $next = action('Admin\SettingController@cronjob').'#result_box';
            return redirect()->away($next);
        }

        $respone['action'] = 'cronjob';

        return view('admin.settings.cronjob', $respone);
    }

    /**
     * Mailer settings.
     *
     * @return \Illuminate\Http\Response
     */
    public function mailer(Request $request)
    {
        if ($request->user()->admin->getPermission('setting_general') != 'yes') {
            return $this->notAuthorized();
        }

        if ($request->old('env')) {
            $mailSettings = $request->old('env');
        } else {
            // SMTP
            $mailSettings = [
                'MAIL_MAILER' => Setting::get('mailer.mailer') ?: Setting::get('mailer.driver'), // Laravel 5.8 compatibility
                'MAIL_HOST' => Setting::get('mailer.host'),
                'MAIL_PORT' => Setting::get('mailer.port'),
                'MAIL_USERNAME' => Setting::get('mailer.username'),
                'MAIL_PASSWORD' => Setting::get('mailer.password'),
                'MAIL_ENCRYPTION' => Setting::get('mailer.encryption'),
                'MAIL_FROM_ADDRESS' => Setting::get('mailer.from.address'),
                'MAIL_FROM_NAME' => Setting::get('mailer.from.name'),
                'sendmail_path' => Setting::get('mailer.sendmail_path') ?: "/usr/sbin/sendmail",
            ];
        }

        $rules = [
            'smtp' => [
                'env.MAIL_MAILER' => 'required',
                'env.MAIL_HOST' => 'required',
                'env.MAIL_PORT' => 'required',
                'env.MAIL_USERNAME' => 'required',
                'env.MAIL_PASSWORD' => 'required',
                'env.MAIL_FROM_ADDRESS' => 'required|email',
                'env.MAIL_FROM_NAME' => 'required',
            ],
            'sendmail' => [
                'env.MAIL_FROM_ADDRESS' => 'required|email',
                'env.MAIL_FROM_NAME' => 'required',
                'env.sendmail_path' => 'required',
            ],
        ];

        // validate and save posted data
        if ($request->isMethod('post')) {
            // Prenvent save from demo mod
            if (config('app.demo')) {
                return view('somethingWentWrong', ['message' => trans('messages.operation_not_allowed_in_demo')]);
            }

            $mailSettings = $request->env;

            $this->validate($request, $rules[$mailSettings['MAIL_MAILER']]);

            // Test connection if mailer == SMTP
            if ($mailSettings['MAIL_MAILER'] == 'smtp') {
                $moreRules = [];
                $messages = [];
                try {
                    $transport = new \Swift_SmtpTransport($mailSettings['MAIL_HOST'], $mailSettings['MAIL_PORT'], $mailSettings['MAIL_ENCRYPTION']);
                    $transport->setUsername($mailSettings['MAIL_USERNAME']);
                    $transport->setPassword($mailSettings['MAIL_PASSWORD']);
                    $transport->setStreamOptions(array('ssl' => array('allow_self_signed' => true, 'verify_peer' => false, 'verify_peer_name' => false)));
                    $mailer = new \Swift_Mailer($transport);
                    $mailer->getTransport()->start();
                } catch (\Swift_TransportException $e) {
                    $moreRules['smtp_valid'] = 'required';
                    $messages['required'] = $e->getMessage();
                } catch (\Exception $e) {
                    $moreRules['smtp_valid'] = 'required';
                    $messages['required'] = $e->getMessage();
                }

                // Trick: make validation failed if SMTP test fails
                $this->validate($request, $moreRules, $messages);
            }

            // update settings table
            Setting::set('mailer.mailer', $mailSettings['MAIL_MAILER']);
            Setting::set('mailer.host', $mailSettings['MAIL_HOST']);
            Setting::set('mailer.port', $mailSettings['MAIL_PORT']);
            Setting::set('mailer.encryption', $mailSettings['MAIL_ENCRYPTION']);
            Setting::set('mailer.username', $mailSettings['MAIL_USERNAME']);
            Setting::set('mailer.password', $mailSettings['MAIL_PASSWORD']);
            Setting::set('mailer.from.name', $mailSettings['MAIL_FROM_NAME']);
            Setting::set('mailer.from.address', $mailSettings['MAIL_FROM_ADDRESS']);
            Setting::set('mailer.sendmail_path', $mailSettings['sendmail_path']);

            // Redirect to my lists page
            $next = action('Admin\SettingController@mailer');
            $request->session()->flash('alert-success', trans('messages.setting.updated'));

            return redirect()->away($next);
        }

        return view('admin.settings.mailer', [
            'rules' => $rules,
            'env' => $mailSettings,
            'action' => 'mailer',
        ]);
    }

    /**
     * Mailer settings.
     *
     * @return \Illuminate\Http\Response
     */
    public function mailerTest(Request $request)
    {
        if ($request->user()->admin->getPermission('setting_general') != 'yes') {
            return $this->notAuthorized();
        }

        // validate and save posted data
        if ($request->isMethod('post')) {
            // Prenvent save from demo mod
            if (config('app.demo')) {
                return view('somethingWentWrong', ['message' => trans('messages.operation_not_allowed_in_demo')]);
            }

            try {
                // build the message
                $message = new ExtendedSwiftMessage();
                $message->setEncoder(new \Swift_Mime_ContentEncoder_PlainContentEncoder('8bit'));
                $message->setContentType('text/html; charset=utf-8');

                $message->setSubject($request->input('subject'));
                $message->setTo($request->input('to_email'));
                $message->addPart($request->input('content'), 'text/html');

                $mailer = App::make('xmailer');
                $result = $mailer->sendWithDefaultFromAddress($message);
                return response()->json(['status' => 'ok'], 200);
            } catch (\Exception $e) {
                return response()->json([
                    'error' => $e->getMessage(),
                ], 400);
            }
        }

        return view('admin.settings.mailerTest');
    }

    /**
     * Update all urls.
     *
     * @return \Illuminate\Http\Response
     */
    public function updateUrls(Request $request)
    {
        // capture the current url, write to .env
        \Acelle\Helpers\reset_app_url(true); // force update

        // @todo for some reason the Artisan::call('config:clear') does not work
        if (file_exists(base_path('bootstrap/cache/config.php'))) {
            unlink(base_path('bootstrap/cache/config.php'));
        }

        if ($request->user()->admin->getPermission('setting_system_urls') != 'yes') {
            return $this->notAuthorized();
        }

        Setting::set('url_unsubscribe', action('CampaignController@unsubscribe', ['message_id' => 'MESSAGE_ID', 'subscriber' => 'SUBSCRIBER']));
        Setting::set('url_open_track', action('CampaignController@open', ['message_id' => 'MESSAGE_ID']));
        Setting::set('url_click_track', action('CampaignController@click', ['message_id' => 'MESSAGE_ID', 'url' => 'URL']));
        Setting::set('url_delivery_handler', action('DeliveryController@notify', ['stype' => '']));
        Setting::set(
            'url_update_profile',
            action('PageController@profileUpdateForm', array(
            'list_uid' => 'LIST_UID',
            'uid' => 'SUBSCRIBER_UID',
            'code' => 'SECURE_CODE', ))
        );
        Setting::set('url_web_view', action('CampaignController@webView', ['message_id' => 'MESSAGE_ID']));

        // Check again to clean up notification
        SystemUrl::check();

        // Redirect to my lists page
        $request->session()->flash('alert-success', trans('messages.setting.updated'));

        return redirect()->action('Admin\SettingController@urls');
    }

    /**
     * View system logs.
     *
     * @return \Illuminate\Http\Response
     */
    public function logs(Request $request)
    {
        $path = base_path('artisan');
        $lines = 300;

        $error_logs = '';
        $file = file($path);
        for ($i = max(0, count($file) - $lines); $i < count($file); ++$i) {
            $error_logs .= $file[$i];
        }

        return view('admin.settings.logs', [
            'error_logs' => $error_logs,
        ]);
    }

    /**
     * View system logs.
     *
     * @return \Illuminate\Http\Response
     */
    public function download_log(Request $request)
    {
        $path = storage_path('logs/'.$request->file);

        return response()->download($path);
    }

    /**
     * License settings.
     *
     * @return \Illuminate\Http\Response
     */
    public function license(Request $request)
    {
        if ($request->user()->admin->getPermission('setting_general') != 'yes') {
            return $this->notAuthorized();
        }

        // validate and save posted data
        if ($request->isMethod('post')) {
            // Prenvent save from demo mod
            if (config('app.demo')) {
                return view('somethingWentWrong', ['message' => trans('messages.operation_not_allowed_in_demo')]);
            }

            try {
                LicenseHelper::updateLicense($request->license);

                // Redirect to my lists page
                $request->session()->flash('alert-success', trans('messages.license.updated'));

                return redirect()->action('Admin\SettingController@license');
            } catch (\Throwable $ex) {
                $license_error = $ex->getMessage();
            }
        }

        return view('admin.settings.license', [
            'license' => LicenseHelper::getCurrentLicense(),
            'license_error' => isset($license_error) ? $license_error : '',
            'action' => 'license' // highlight "License" tab
        ]);
    }

    /**
     * Upgrade manager page.
     *
     * @return \Illuminate\Http\Response
     */
    public function upgrade(Request $request)
    {
        Log::info('Going to @upgrade page');
        // secret key to send to the verification server
        session(['secret' => $request->input('secret')]);

        // Upgrade manager
        Log::info('Initiate upgrade manager');
        $manager = new UpgradeManager();

        return view('admin.settings.upgrade', [
            'license' => LicenseHelper::getCurrentLicense(),
            'any' => 'any',
            'manager' => $manager,
            'phpversion' => version_compare(PHP_VERSION, config('custom.php_recommended'), '>='),
            'action' => 'upgrade',
        ]);
    }

    /**
     * Upgrade manager page.
     *
     * @return \Illuminate\Http\Response
     */
    public function doUpgrade(Request $request)
    {
        if ($request->user()->admin->getPermission('setting_upgrade_manager') != 'yes') {
            return $this->notAuthorized();
        }

        $manager = new UpgradeManager();
        $failed = $manager->test();

        if (empty($failed)) {
            // RUN UPGRADE
            Log::info('Actually run.....');
            $manager->run();

            Log::info('System successfully upgraded to the new version');
            Log::info('Redirecting to work with new request');
            $request->session()->put('upgraded', true);

            // REFRESH by redirecting to an entirely new page
            // Then make a new request from browser to load new config
            // A normal redirect() will retain the current setting

            // Do not use action('...'), the route may not exist yet
            //     $pageUrl = action('Admin\Upgrade@migrate');
            // Use route()
            $pageUrl = url('/migrate/run');
            echo '<html>
                <head>
                    <meta http-equiv="refresh" content="3;'.$pageUrl.'" />
                <title>Application Upgrade...</title>
                </head>
                <body>
                    Upgrade is in progress, please wait...
                </body>
                </html>';

            return;
        } else {
            Log::warning('Cannot upgrade, certain files are not writable');
            return view('admin.settings.upgrade', [
                'any' => 'any',
                'manager' => $manager,
                'failed' => $failed,
                'action' => 'upgrade',
            ]);
        }
    }

    /**
     * Cancel upgrade and delete the uploaded file.
     *
     * @return \Illuminate\Http\Response
     */
    public function cancelUpgrade(Request $request)
    {
        if ($request->user()->admin->getPermission('setting_upgrade_manager') != 'yes') {
            return $this->notAuthorized();
        }

        try {
            $manager = new UpgradeManager();
            $manager->cleanup();
            $request->session()->flash('alert-info', trans('messages.upgrade.alert.cancel_success'));
        } catch (\Exception $e) {
            Log::info('Something went wrong while cancelling upgrade. '.$e->getMessage());
            $request->session()->flash('alert-error', $e->getMessage());
        }

        return redirect()->action('Admin\SettingController@upgrade');
    }

    /**
     * Upload the application patch.
     *
     * @return \Illuminate\Http\Response
     */
    public function uploadApplicationPatch(Request $request)
    {
        if (is_null(\Acelle\Helpers\LicenseHelper::getCurrentLicense())) {
            $request->session()->flash('alert-error', 'Please <a style="text-decoration:underline;font-style: normal;" href="'.url('admin/settings/license').'">register</a> your installation with a valid purchase code first before upgrading.');

            return redirect()->action('Admin\SettingController@upgrade');
        }

        if ($request->user()->admin->getPermission('setting_upgrade_manager') != 'yes') {
            return $this->notAuthorized();
        }

        try {
            $manager = new UpgradeManager();
            // if file size exceeds "upload_max_filesize" ini directive
            // moving will end up fail with an exception
            // also, file('file')->path() will return the application root directory rather than the correct file path
            $request->file('file')->move(storage_path('tmp'), $request->file('file')->getClientOriginalName());
            $path = storage_path('tmp/'.$request->file('file')->getClientOriginalName());
            $manager->load($path);
            $request->session()->flash('alert-success', trans('messages.upgrade.alert.upload_success'));
        } catch (\Exception $e) {
            Log::info('Upgrade failed. '.$e->getMessage());
            $request->session()->flash('alert-error', $e->getMessage());
        }

        return redirect()->action('Admin\SettingController@upgrade');
    }

    /**
     * Advanced settings.
     *
     * @return \Illuminate\Http\Response
     */
    public function advanced(Request $request)
    {
        if ($request->user()->admin->getPermission('setting_general') != 'yes') {
            return $this->notAuthorized();
        }

        // Setting::updateAll();
        $settings = Setting::getAll();
        if (null !== $request->old()) {
            foreach ($request->old() as $name => $value) {
                if (isset($settings[$name])) {
                    $settings[$name]['value'] = $value;
                }
            }
        }

        // validate and save posted data
        if ($request->isMethod('post')) {
            // Prenvent save from demo mod
            if (config('app.demo')) {
                return view('somethingWentWrong', ['message' => trans('messages.operation_not_allowed_in_demo')]);
            }

            $rules = [];
            $this->validate($request, $rules);

            // Save settings
            foreach ($request->all() as $name => $value) {
                if ($name != '_token' && isset($settings[$name])) {
                    // Upload and save image
                    if ($name == 'site_logo_light' || $name == 'site_logo_dark') {
                        if ($request->hasFile($name) && $request->file($name)->isValid()) {
                            Setting::uploadSiteLogo($request->file($name), $name);
                        }
                    } else {
                        if ($settings[$name]['cat'] == 'advanced' && $request->user()->admin->getPermission('setting_general') == 'yes') {
                            Setting::set($name, $value);
                        }
                    }
                }
            }

            // Redirect to my lists page
            $request->session()->flash('alert-success', trans('messages.setting.updated'));

            return redirect()->action('Admin\SettingController@advanced');
        }

        return view('admin.settings.advanced', [
            'settings' => $settings,
        ]);
    }

    /**
     * Advanced settings.
     *
     * @return \Illuminate\Http\Response
     */
    public function advancedUpdate(Request $request, $name)
    {
        if ($request->user()->admin->getPermission('setting_general') != 'yes') {
            return $this->notAuthorized();
        }

        if (config('app.demo')) {
            return $this->notAuthorized();
        }

        // update setting value
        Setting::set($name, $request->value);

        echo trans('messages.setting.update.success', ['name' => $name]);
    }

    /**
     * Payment gateway settings.
     *
     * @return \Illuminate\Http\Response
     */
    public function payment(Request $request)
    {
        if ($request->user()->admin->getPermission('setting_general') != 'yes') {
            return $this->notAuthorized();
        }

        // Setting::updateAll();
        $settings = Setting::getAll();

        if (config('app.demo')) {
            return $this->notAuthorized();
        }

        $rules = [
            'auto_billing_period' => 'required',
            'allowed_due_subscription' => 'required',
        ];
        $this->validate($request, $rules);

        // Save settings
        Setting::set('allowed_due_subscription', $request->allowed_due_subscription);
        Setting::set('subscription.auto_billing_period', $request->auto_billing_period);
        Setting::set('not_require_card_for_trial', $request->not_require_card_for_trial);

        // Redirect to my lists page
        $request->session()->flash('alert-success', trans('messages.setting.updated'));

        return redirect()->action('Admin\PaymentController@index');
    }

    /**
     * Upgrade from URL.
     *
     * @return \Illuminate\Http\Response
     */
    public function upgradeFromUrl(Request $request)
    {
        if ($request->isMethod('post')) {
            $downloader = new Downloader($request->input('url'));
            $tmpPath = storage_path('tmp/upgrade.bin.zip');
            $downloader->downloadTo($tmpPath);

            $manager = new UpgradeManager();
            $manager->load($tmpPath);
            $failed = $manager->test();

            if (!empty($failed)) {
                Log::warning('Cannot upgrade, certain files are not writable');
                return view('admin.settings.upgrade', [
                    'any' => 'any',
                    'manager' => $manager,
                    'failed' => $failed,
                ]);
            }

            // RUN UPGRADE
            Log::info('Actually run from URL.....');
            $manager->run();

            Log::info('System successfully upgraded to the new version');
            Log::info('Redirecting to work with new request');
            $request->session()->put('upgraded', true);

            // REFRESH by redirecting to an entirely new page
            // Then make a new request from browser to load new config
            // A normal redirect() will retain the current setting

            // Do not use action('...'), the route may not exist yet
            //     $pageUrl = action('Admin\Upgrade@migrate');
            // Use route()
            $pageUrl = url('/migrate/run');
            echo '<html>
                <head>
                    <meta http-equiv="refresh" content="3;'.$pageUrl.'" />
                <title>Application Upgrade...</title>
                </head>
                <body>
                    Upgrade is in progress, please wait...
                </body>
                </html>';

            return;
        }

        // GET ONLY
        return view('admin.settings.upgradeFromUrl');
    }

    public function licenseRemove()
    {
        try {
            LicenseHelper::removeLicense();

            return redirect()->action('Admin\SettingController@license')
                ->with('alert-success', trans('messages.license.removed'));
        } catch (\Exception $ex) {
            return redirect()->action('Admin\SettingController@license')
                ->with('alert-error', $ex->getMessage());
        }
    }
}
