<?php

namespace Acelle\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Acelle\Model\Automation2;
use Acelle\Model\Notification;
use Acelle\Cashier\Cashier;
use Acelle\Model\Subscription;
use Acelle\Model\Setting;
use Acelle\Model\Campaign;
use Laravel\Tinker\Console\TinkerCommand;
use Exception;
use Acelle\Library\Facades\SubscriptionFacade;
use Acelle\Helpers\LicenseHelper;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [

    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     */
    protected function schedule(Schedule $schedule)
    {
        if (!isInitiated()) {
            return;
        }

        // Make sure CLI process is NOT executed as root
        Notification::recordIfFails(function () {
            if (!exec_enabled()) {
                throw new Exception('The exec() function is missing or disabled on the hosting server');
            }

            if (exec('whoami') == 'root') {
                throw new Exception("Cronjob process is executed by 'root' which might cause permission issues. Make sure the cronjob process owner is the same as the acellemail/ folder's owner");
            }
        }, 'CronJob issue');

        $schedule->call(function () {
            event(new \Acelle\Events\CronJobExecuted());
        })->name('cronjob_event:log')->everyMinute();

        // Automation2
        $schedule->call(function () {
            Automation2::run();
        })->name('automation:run')->everyFiveMinutes();

        // Bounce/feedback handler
        $schedule->command('handler:run')->everyThirtyMinutes();

        // Queued import/export/campaign
        // Allow overlapping: max 10 proccess as a given time (if cronjob interval is every minute)
        // Job is killed after timeout
        $schedule->command('queue:work --queue=default,batch --timeout=120 --tries=1 --max-time=180')->everyMinute();

        // Make it more likely to have a running queue at any given time
        // Make sure it is stopped before another queue listener is created
        // $schedule->command('queue:work --queue=default,batch --timeout=120 --tries=1 --max-time=290')->everyFiveMinutes();

        // Sender verifying
        $schedule->command('sender:verify')->everyFiveMinutes();

        // System clean up
        $schedule->command('system:cleanup')->daily();

        // GeoIp database check
        $schedule->command('geoip:check')->everyMinute()->withoutOverlapping(60);

        // Subscription: check expiration
        $schedule->call(function () {
            SubscriptionFacade::endExpiredSubscriptions();
            SubscriptionFacade::createRenewInvoices();
            SubscriptionFacade::autoChargeRenewInvoices();
        })->name('subscription:monitor')->everyFiveMinutes();

        // Check for scheduled campaign to execute
        $schedule->call(function () {
            Campaign::checkAndExecuteScheduledCampaigns();
        })->name('check_and_execute_scheduled_campaigns')->everyMinute();

        $licenseTask = $schedule->call(function () {
            Notification::recordIfFails(
                function () {
                    $license = LicenseHelper::getCurrentLicense();

                    if (is_null($license)) {
                        throw new Exception(trans('messages.license.error.no_license'));
                    }

                    LicenseHelper::refreshLicense();
                },
                $title = trans('messages.license.error.invalid'),
                $exceptionCallback = null,
            );
        })->name('verify_license');

        if (config('custom.japan')) {
            $licenseTask->everyMinute();
        } else {
            $licenseTask->weeklyOn(rand(1, 6), '10:'.rand(10, 59)); // randomly from Mon to Sat, at 10:10 - 10:59
        }
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
