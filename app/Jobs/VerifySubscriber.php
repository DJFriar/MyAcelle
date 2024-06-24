<?php

namespace Acelle\Jobs;

use Illuminate\Bus\Batchable;
use Acelle\Library\Exception\NoCreditsLeft;
use Acelle\Library\Exception\RateLimitExceeded;
use Acelle\Library\Exception\VerificationTakesLongerThanNormal;
use Exception;
use Closure;

use function Acelle\Helpers\execute_with_limits;

class VerifySubscriber extends Base
{
    use Batchable;

    public $timeout = 120;
    public $maxExceptions = 1; // This is required if retryUntil is used, otherwise, the default value is 255
    public $failOnTimeout = true;

    protected $server;
    protected $subscriber;
    protected $subscription;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($subscriber, $server, $subscription)
    {
        $this->subscriber = $subscriber;
        $this->server = $server;
        $this->subscription = $subscription;
    }

    /**
     * Determine the time at which the job should timeout.
     *
     * @return \DateTime
     */
    public function retryUntil()
    {
        return now()->addHours(12);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->batch()->cancelled()) {
            return;
        }

        $this->doVerify();
    }

    public function doVerify(Closure $exceptionCallback = null)
    {
        try {
            // Count related quota trackers
            $rateTrackers = [
                $this->server->getRateTracker()
            ];

            // Credit limit tracker
            if (is_null($this->subscription)) {
                $creditTrackers = [];
            } else {
                $creditTrackers = [
                    $this->subscription->getVerifyEmailCreditTracker()
                ];
            }

            execute_with_limits($rateTrackers, $creditTrackers, function () {
                $this->subscriber->verify($this->server);
            });
        } catch (VerificationTakesLongerThanNormal $ex) {
            // Just ignore and return
            // Warn user that there are certain subscribers that are skipped
            // @important: silently quit leaving subscribers not verified
            return;
        } catch (RateLimitExceeded $ex) {
            if (!is_null($exceptionCallback)) {
                return $exceptionCallback($ex);
            }

            // Release the job, have it try again after 60 seconds and (hopefully) the quota limits will be lifted then as time goes by
            $this->release(60);
        } catch (Exception $ex) {
            if (!is_null($exceptionCallback)) {
                return $exceptionCallback($ex);
            }

            throw $ex;
        }
    }
}
