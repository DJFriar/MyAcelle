<?php

namespace Acelle\Http\Controllers;

use Illuminate\Http\Request;
use Acelle\Model\Notification as AppNotification;
use Illuminate\Support\Facades\Http;

class InstallController extends Controller
{
    // check for current step
    public function step($request)
    {
        $step = 0;

        $data = $request->session()->get('compatibilities');
        if (isset($data)) {
            $step = 1;
        } else {
            return $step;
        }

        $data = $request->session()->get('site_info');
        if (isset($data)) {
            $step = 3;
        } else {
            return $step;
        }

        $data = $request->session()->get('database');
        if (isset($data)) {
            $step = 4;
        } else {
            return $step;
        }

        $data = $request->session()->get('database_imported');
        if (isset($data)) {
            $step = 5;
        } else {
            return $step;
        }

        $data = $request->session()->get('cron_jobs');
        if (isset($data)) {
            $step = 6;
        } else {
            return $step;
        }

        return $step;
    }

    // Starting installation
    public function starting(Request $request)
    {
        $next = action('InstallController@systemCompatibility');
        return redirect()->away($next);
    }

    public function systemCompatibility(Request $request)
    {
        // Begin check
        $request->session()->forget('compatibilities');

        $compatibilities = $this->checkSystemCompatibility();
        $result = true;
        foreach ($compatibilities as $compatibility) {
            if (!$compatibility['check']) {
                $result = false;
            }
        }

        // retry if something not work yet
        try {
            if ($result) {
                $request->session()->put('compatibilities', $compatibilities);
            }

            return view('install.compatibilities', [
                'compatibilities' => $compatibilities,
                'result' => $result,
                'step' => $this->step($request),
                'current' => 1,
            ]);
        } catch (\Exception $e) {
            $next_page = action('InstallController@systemCompatibility');
            return redirect()->away($next_page);
        }
    }

    public function siteInfo(Request $request)
    {
        if ($this->step($request) < 1) {
            return redirect()->action('InstallController@systemCompatibility');
        }

        // make sure session is working
        $rules = [
            'site_name' => 'required',
            'site_keyword' => 'required',
            'site_description' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'timezone' => 'required',
        ];
        $mail_rules = [
            'smtp' => [
                'smtp_hostname' => 'required',
                'smtp_port' => 'required',
                'smtp_username' => 'required',
                'smtp_password' => 'required',
                'mail_from_email' => 'required|email',
                'mail_from_name' => 'required',
            ],
            'sendmail' => [
                'mail_from_email' => 'required|email',
                'mail_from_name' => 'required',
                'sendmail_path' => 'required',
            ],
        ];
        $smtp_rules = $mail_rules['smtp'];

        // validate and save posted data
        if ($request->isMethod('post')) {
            $request->session()->forget('site_info');

            $rules = array_merge($rules, $mail_rules[$request->mail_mailer]);
            $this->validate($request, $rules);

            $site_info = $request->all();

            // Check license
            if (!empty($request->license)) {
                try {
                    $license = \Acelle\Helpers\LicenseHelper::getLicense($site_info["license"]);
                    $site_info["license"] = $license['purchase_code'];
                    $site_info["license_type"] = $license['licence'];
                    $site_info["license_status"] = $license['status'];
                    $site_info["license_supported_until"] = $license['supported_until'];
                } catch (\Exception $ex) {
                    $error = \Illuminate\Validation\ValidationException::withMessages([
                       'license' => $ex->getMessage(),
                    ]);

                    throw $error;
                }
            } else {
                $site_info["license"] = '';
                $site_info["license_type"] = '';
                $site_info["license_status"] = '';
                $site_info["license_supported_until"] = '';
            }

            // Check SMTP connection
            if ($request->mail_mailer == 'smtp') {
                $rules = [];
                try {
                    $transport = new \Swift_SmtpTransport($request->smtp_hostname, $request->smtp_port, $request->smtp_encryption);
                    $transport->setUsername($request->smtp_username);
                    $transport->setPassword($request->smtp_password);
                    $mailer = new \Swift_Mailer($transport);
                    $mailer->getTransport()->start();
                } catch (\Swift_TransportException $e) {
                    $rules['smtp_valid'] = 'required';
                } catch (Exception $e) {
                    $rules['smtp_valid'] = 'required';
                }
                $this->validate($request, $rules);
            }

            $request->session()->put('site_info', $site_info);

            return redirect()->action('InstallController@database');
        }

        $site_info = $request->session()->get('site_info');
        if (!empty($request->old())) {
            $site_info = $request->old();
        }

        return view('install.site_info', [
            'site_info' => $site_info,
            'rules' => $rules,
            'smtp_rules' => $smtp_rules,
            'step' => $this->step($request),
            'current' => 2,
        ]);
    }

    // Database configuration
    public function database(Request $request)
    {
        if ($this->step($request) < 2) {
            return redirect()->action('InstallController@siteInfo');
        }

        // Check required fields
        $rules = array(
            'hostname' => 'required',
            'port' => 'required',
            'username' => 'required',
            'password' => 'required',
            'database_name' => 'required',
        );

        // validate and save posted data
        if ($request->isMethod('post')) {
            $request->session()->forget('database');

            $this->validate($request, $rules);

            // Check mysql connection
            try {
                $port = $request->port;
                $port = (int) $port;
                $conn = new \mysqli($request->hostname, $request->username, $request->password, $request->database_name, $port);
            } catch (\Exception $e) {
                $rules['mysql_connection'] = 'required';
                $request->session()->flash('alert-error', $e->getMessage());
            }

            $this->validate($request, $rules);

            // Save database session
            $database = $request->all();
            $request->session()->put('database', $database);

            $next_page = action('InstallController@databaseImport');

            // write config file
            $this->writeEnv($request);

            return redirect()->away($next_page);
        }

        $database = $request->session()->get('database');
        if (!empty($request->old())) {
            $database = $request->old();
        }

        return view('install.database', [
            'database' => $database,
            'rules' => $rules,
            'step' => $this->step($request),
            'current' => 3,
        ]);
    }

    public function import(Request $request)
    {
        if ($this->step($request) < 3) {
            return redirect()->action('InstallController@database');
        }

        $database = $request->session()->get('database');
        $site_info = $request->session()->get('site_info');

        // connect mysql
        $mysqli = new \mysqli($database['hostname'], $database['username'], $database['password'], $database['database_name'], $database['port']);

        $request->session()->forget('database_imported');

        // Drop all EXISTING tables
        $sql = "SHOW TABLE STATUS";
        $statement = $mysqli->query($sql);
        while ($result = $statement->fetch_object()) {
            $tableName = $result->Name;

            // drop the table
            $mysqli->query("SET FOREIGN_KEY_CHECKS = 0;");
            $mysqli->query("DROP TABLE IF EXISTS {$tableName};");
            $mysqli->query("SET FOREIGN_KEY_CHECKS = 1;");
        }

        $request->session()->put('database_imported', true);
        $next_page = action('InstallController@cronJobs');

        // Run migrate
        artisan_migrate();

        // import database via seeder
        \Artisan::call('db:seed', ['--class' => 'DatabaseInit']);

        // get database connection
        $database = $request->session()->get('database');
        $mysqli = new \mysqli($database['hostname'], $database['username'], $database['password'], $database['database_name'], $database['port']);

        // default date
        $date = '2023-01-01 00:00:00';

        // Insert default superuser
        $mysqli->query(sprintf('DELETE FROM %s WHERE 1', table('users')));

        $user = new \Acelle\Model\User();
        $user->api_token = str_random(60);
        $user->email = $site_info['email'];
        $user->first_name = $site_info['first_name'];
        $user->last_name = $site_info['last_name'];
        $user->password = bcrypt($site_info['password']);
        $user->status = 'active';
        $user->activated = 1;
        $user->save();

        // Create admin with appropriate locale / language_id
        $mysqli->query(sprintf('DELETE FROM %s WHERE 1', table('admins')));

        $locale = \App::getLocale();
        $language = \Acelle\Model\Language::getFirstLanguageByCode($locale);

        $admin = new \Acelle\Model\Admin();
        $admin->user_id = 1;
        $admin->admin_group_id = 1;
        $admin->language_id = $language->id;
        $admin->timezone = config('app.timezone');
        $admin->status = 'active';
        $admin->menu_layout = \Acelle\Model\Setting::get('layout.menu_bar');
        $admin->save();

        if ($site_info['create_customer_account'] == 'yes') {
            $mysqli->query(sprintf('DELETE FROM %s WHERE 1', table('customers')));
            $customer = $admin->createCustomerAccount();
            $user->customer_id = $customer->id;
            $user->save();
        }

        // Insert system urls settings
        \Acelle\Model\Setting::set('url_delivery_handler', action('DeliveryController@notify', ['stype' => '']));
        \Acelle\Model\Setting::set('url_unsubscribe', action('CampaignController@unsubscribe', ['message_id' => 'MESSAGE_ID', 'subscriber' => 'SUBSCRIBER']));
        \Acelle\Model\Setting::set('url_open_track', action('CampaignController@open', ['message_id' => 'MESSAGE_ID']));
        \Acelle\Model\Setting::set('url_web_view', action('CampaignController@webView', ['message_id' => 'MESSAGE_ID']));
        \Acelle\Model\Setting::set('url_click_track', action('CampaignController@click', ['message_id' => 'MESSAGE_ID', 'url' => 'URL']));
        \Acelle\Model\Setting::set('url_update_profile', action('PageController@profileUpdateForm', ['list_uid' => 'LIST_UID', 'uid' => 'SUBSCRIBER_UID', 'code' => 'SECURE_CODE']));

        // Site info settings
        \Acelle\Model\Setting::set('site_name', $site_info['site_name']);
        \Acelle\Model\Setting::set('site_keyword', $site_info['site_keyword']);
        \Acelle\Model\Setting::set('site_description', $site_info['site_description']);
        \Acelle\Model\Setting::set('license', $site_info['license']);
        \Acelle\Model\Setting::set('license_type', $site_info['license_type']);

        // mail setting
        \Acelle\Model\Setting::set('mailer.mailer', $site_info['mail_mailer']);
        \Acelle\Model\Setting::set('mailer.host', $site_info['smtp_hostname']);
        \Acelle\Model\Setting::set('mailer.port', $site_info['smtp_port']);
        \Acelle\Model\Setting::set('mailer.encryption', $site_info['smtp_encryption']);
        \Acelle\Model\Setting::set('mailer.username', $site_info['smtp_username']);
        \Acelle\Model\Setting::set('mailer.password', $site_info['smtp_password']);
        \Acelle\Model\Setting::set('mailer.from.name', $site_info['mail_from_name']);
        \Acelle\Model\Setting::set('mailer.from.address', $site_info['mail_from_email']);
        \Acelle\Model\Setting::set('mailer.sendmail_path', $site_info['sendmail_path']);

        // Generate translation files if not exist
        // Cashier plugin for example
        \Acelle\Model\Language::dump();

        // Just finish
        $request->session()->flash('alert-success', trans('messages.install.database_import.success'));

        return redirect()->away($next_page);
    }

    // Import Database
    public function databaseImport(Request $request)
    {
        if ($this->step($request) < 3) {
            return redirect()->action('InstallController@database');
        }

        $database = $request->session()->get('database');
        $site_info = $request->session()->get('site_info');

        // connect mysql
        $mysqli = new \mysqli($database['hostname'], $database['username'], $database['password'], $database['database_name'], $database['port']);

        return view('install.database_import', [
            'database' => $database,
            'step' => $this->step($request),
            'current' => 3
        ]);
    }

    public function cronJobs(Request $request)
    {
        if ($this->step($request) < 5) {
            return redirect()->action('InstallController@database');
        }

        $respone = \Acelle\Library\Tool::cronjobUpdateController($request, $this);
        if ($respone == 'done' || $respone == 'remote') {
            return redirect()->action('InstallController@finishing');
        }

        return view('install.cron_jobs', $respone);
    }

    public function finishing(Request $request)
    {
        $next_page = action('InstallController@finish');
        AppNotification::cleanup();

        return redirect()->away($next_page);
    }

    public function finish(Request $request)
    {
        if ($this->step($request) < 6) {
            return redirect()->action('InstallController@database');
        }

        $request->session()->put('install_finish', true);

        $file_path = storage_path('app/installed');
        $file = fopen($file_path, 'w') or die('Unable to open file!');
        fwrite($file, '');
        fclose($file);

        // Generate translation files if not exist (again!)
        // Cashier plugin for example
        // Notice that the following should be executed after the installed file is avaialble
        // Otherwise, the service provider of Cashier will not trigger
        \Acelle\Model\Language::dump();

        return view('install.finish', [
            'step' => $this->step($request),
            'current' => 6,
        ]);
    }

    // Check for requirement when install app
    public function checkSystemCompatibility()
    {
        return [
            [
                'type' => 'requirement',
                'name' => 'PHP',
                'check' => version_compare(PHP_VERSION, config('custom.php'), '>='),
                'note' => trans('messages.install.error.php', ['version' => config('custom.php')]),
            ],
            [
                'type' => 'requirement',
                'name' => 'MySQLi',
                'check' => function_exists('mysqli_connect'),
                'note' => trans('messages.install.error.extension', ['name' => 'MySQLi']),
            ],
            [
                'type' => 'requirement',
                'name' => 'OpenSSL',
                'check' => extension_loaded('openssl'),
                'note' => trans('messages.install.error.extension', ['name' => 'OpenSSL']),
            ],
            [
                'type' => 'requirement',
                'name' => 'Multibyte String',
                'check' => extension_loaded('mbstring'),
                'note' => trans('messages.install.error.extension', ['name' => 'Multibyte String']),
            ],
            [
                'type' => 'requirement',
                'name' => 'PHP PDO',
                'check' => extension_loaded('pdo'),
                'note' => trans('messages.install.error.extension', ['name' => 'PHP PDO']),
            ],
            [
                'type' => 'requirement',
                'name' => 'Tokenizer',
                'check' => extension_loaded('tokenizer'),
                'note' => trans('messages.install.error.extension', ['name' => 'Tokenizer']),
            ],
            [
                'type' => 'requirement',
                'name' => 'PHP ZIP',
                'check' => class_exists('ZipArchive', false),
                'note' => trans('messages.install.error.extension', ['name' => 'PHP ZIP']),
            ],
            [
                'type' => 'requirement',
                'name' => 'IMAP',
                'check' => extension_loaded('imap'),
                'note' => trans('messages.install.error.extension', ['name' => 'IMAP']),
            ],
            [
                'type' => 'requirement',
                'name' => 'SQLite3',
                'check' => class_exists('SQLite3'),
                'note' => trans('messages.install.error.extension', ['name' => 'SQLite3']),
            ],
            [
                'type' => 'requirement',
                'name' => 'PHP GD',
                'check' => (extension_loaded('gd') && function_exists('gd_info')),
                'note' => trans('messages.install.error.extension', ['name' => 'PHP GD']),
            ],
            [
                'type' => 'requirement',
                'name' => 'PHP FileInfo',
                'check' => extension_loaded('fileinfo'),
                'note' => trans('messages.install.error.extension', ['name' => 'PHP FileInfo']),
            ],
            [
                'type' => 'requirement',
                'name' => 'PHP Curl',
                'check' => extension_loaded('curl'),
                'note' => trans('messages.install.error.extension', ['name' => 'PHP Curl']),
            ],
            [
                'type' => 'requirement',
                'name' => 'PHP XML',
                'check' => extension_loaded('xml'),
                'note' => trans('messages.install.error.extension', ['name' => 'PHP XML']),
            ],
            [
                'type' => 'requirement',
                'name' => 'proc_close()',
                'check' => func_enabled('proc_close'),
                'note' => trans('messages.install.error.function', ['name' => 'proc_close()']),
            ],
            [
                'type' => 'requirement',
                'name' => 'escapeshellarg()',
                'check' => func_enabled('escapeshellarg'),
                'note' => trans('messages.install.error.function', ['name' => 'escapeshellarg()']),
            ],
            [
                'type' => 'permission',
                'name' => base_path('storage/app'),
                'check' => file_exists(base_path('/storage/app')) &&
                    is_dir(base_path('/storage/app')) &&
                    (is_writable(base_path('/storage/app'))),
                'note' => trans('messages.install.notice.writable'),
            ],
            [
                'type' => 'permission',
                'name' => base_path('storage/framework'),
                'check' => file_exists(base_path('/storage/framework')) && is_dir(base_path('/storage/framework')) && (is_writable(base_path('/storage/framework'))),
                'note' => tRans('messages.install.notice.writable'),
            ],
            [
                'type' => 'permission',
                'name' => base_path('storage/logs'),
                'check' => file_exists(base_path('/storage/logs')) && is_dir(base_path('/storage/logs')) && (is_writable(base_path('/storage/logs'))),
                'note' => tRans('messages.install.notice.writable'),
            ],
            [
                'type' => 'permission',
                'name' => base_path('storage/job'),
                'check' => file_exists(base_path('/storage/job')) && is_dir(base_path('/storage/job')) && (is_writable(base_path('/storage/job'))),
                'note' => tRans('messages.install.notice.writable'),
            ],
            [
                'type' => 'permission',
                'name' => base_path('bootstrap/cache'),
                'check' => file_exists(base_path('/bootstrap/cache')) && is_dir(base_path('/bootstrap/cache')) && (is_writable(base_path('/bootstrap/cache'))),
                'note' => tRans('messages.install.notice.writable'),
            ],
        ];
    }

    public function checkServerVar()
    {
        $vars = array('HTTP_HOST', 'SERVER_NAME', 'SERVER_PORT', 'SCRIPT_NAME', 'SCRIPT_FILENAME', 'PHP_SELF', 'HTTP_ACCEPT', 'HTTP_USER_AGENT');
        $missing = array();
        foreach ($vars as $var) {
            if (!isset($_SERVER[$var])) {
                $missing[] = $var;
            }
        }

        if (!empty($missing)) {
            return '$_SERVER does not have: '.implode(', ', $missing);
        }

        if (!isset($_SERVER['REQUEST_URI']) && isset($_SERVER['QUERY_STRING'])) {
            return 'Either $_SERVER["REQUEST_URI"] or $_SERVER["QUERY_STRING"] must exist.';
        }

        if (!isset($_SERVER['PATH_INFO']) && strpos($_SERVER['PHP_SELF'], $_SERVER['SCRIPT_NAME']) !== 0) {
            return 'Unable to determine URL path info. Please make sure $_SERVER["PATH_INFO"] (or $_SERVER["PHP_SELF"] and $_SERVER["SCRIPT_NAME"]) contains proper value.';
        }

        return '';
    }

    public function checkCaptchaSupport()
    {
        if (function_exists('getimagesize')) {
            return '';
        }

        if (extension_loaded('imagick')) {
            $imagick = new Imagick();
            $imagickFormats = $imagick->queryFormats('PNG');
        }

        if (extension_loaded('gd')) {
            $gdInfo = gd_info();
        }

        if (isset($imagickFormats) && in_array('PNG', $imagickFormats)) {
            return '';
        } elseif (isset($gdInfo)) {
            if ($gdInfo['FreeType Support']) {
                return '';
            }

            return 'GD installed,<br />FreeType support not installed';
        }

        return 'GD or ImageMagick not installed';
    }

    // Write configuration values to file
    public function writeEnv($request)
    {
        // get database config
        $database = $request->session()->get('database');
        $database = !empty($database) ? $database : [];

        // get smtp config
        $smtp = $request->session()->get('site_info');
        $smtp = !empty($smtp) ? $smtp : [];

        \Acelle\Helpers\write_envs([
            'APP_URL' => url('/'),
            'DB_HOST' => (isset($database['hostname']) ? $database['hostname'] : ''),
            'DB_DATABASE' => (isset($database['database_name']) ? $database['database_name'] : ''),
            'DB_USERNAME' => (isset($database['username']) ? $database['username'] : ''),
            'DB_PASSWORD' => (isset($database['password']) ? quoteDotEnvValue($database['password']) : ''),
            'DB_PORT' => (isset($database['port']) ? $database['port'] : ''),
            'DB_TABLES_PREFIX' => (isset($database['tables_prefix']) ? $database['tables_prefix'] : ''),
            'MAIL_MAILER' => (isset($smtp['mail_mailer']) ? $smtp['mail_mailer'] : 'mail'),
            'MAIL_HOST' => (isset($smtp['smtp_hostname']) ? $smtp['smtp_hostname'] : ''),
            'MAIL_PORT' => (isset($smtp['smtp_port']) ? $smtp['smtp_port'] : ''),
            'MAIL_USERNAME' => (isset($smtp['smtp_username']) ? $smtp['smtp_username'] : ''),
            'MAIL_PASSWORD' => (isset($smtp['smtp_password']) ? quoteDotEnvValue($smtp['smtp_password']) : ''),
            'MAIL_ENCRYPTION' => (isset($smtp['smtp_encryption']) ? $smtp['smtp_encryption'] : ''),
            'MAIL_FROM_ADDRESS' => (isset($smtp['mail_from_email']) ? $smtp['mail_from_email'] : ''),
            'MAIL_FROM_NAME' => (isset($smtp['mail_from_name']) ? $smtp['mail_from_name'] : ''),
        ]);
    }

    public function importSql($database, $file_path)
    {
        // Name of the file
        $filename = $file_path;
        // MySQL host
        $mysql_host = $database['hostname'];
        // MySQL username
        $mysql_username = $database['username'];
        // MySQL password
        $mysql_password = $database['password'];
        // Database name
        $mysql_database = $database['database_name'];

        // Connect to MySQL server
        $mysqli = new \mysqli($mysql_host, $mysql_username, $mysql_password, $mysql_database, $database['port']);

        /* check connection */
        if ($mysqli->connect_errno) {
            printf("Connect failed: %s\n", $mysqli->connect_error);
            exit();
        }

        // Temporary variable, used to store current query
        $templine = '';
        // Read in entire file
        $lines = file($filename);
        // Loop through each line
        foreach ($lines as $line) {
            // Skip it if it's a comment
            if (substr($line, 0, 2) == '--' || $line == '') {
                continue;
            }

            // Add this line to the current segment
            $templine .= $line;
            // If it has a semicolon at the end, it's the end of the query
            if (substr(trim($line), -1, 1) == ';') {
                // Perform the query
                $mysqli->query($templine) or print 'Error performing query \'<strong>'.$templine.'\': '.$mysqli->connect_error.'<br /><br />';
                // Reset temp variable to empty
                $templine = '';
            }
        }

        unlink($file_path);

        return true;
    }
}
