<?php

namespace Acelle\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Acelle\Model\Campaign;
use Acelle\Model\Email;
use Acelle\Model\Subscriber;
use Acelle\Model\SendingServer;
use Acelle\Model\Subscription;
use Acelle\Library\Exception\RateLimitExceeded;
use Acelle\Library\Exception\OutOfCredits;
use Exception;
use Throwable;

use function Acelle\Helpers\execute_with_limits;

class SendMessage implements ShouldQueue
{
    use Batchable;
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $timeout = 600;
    public $maxExceptions = 1; // This is required if retryUntil is used, otherwise, the default value is 255
    public $failOnTimeout = true;

    // $tries is no longer needed (or effective) due to the retryUntil() method
    // public $tries = 1;

    protected $subscriber;
    protected $server;
    protected $campaign;
    protected $subscription;
    protected $triggerId;
    protected $stopOnError = false;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($campaign, Subscriber $subscriber, SendingServer $server, Subscription $subscription = null, $triggerId = null)
    {
        $this->campaign = $campaign;
        $this->subscriber = $subscriber;
        $this->server = $server;
        $this->subscription = $subscription;
        $this->triggerId = $triggerId;
    }

    public function setStopOnError($value)
    {
        if (!is_bool($value)) {
            throw new Exception('Parameter passed to setStopOnError must be bool');
        }

        $this->stopOnError = $value;
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
        // Remember that this job may not belong to a batch
        if ($this->batch() && $this->batch()->cancelled()) {
            return;
        }

        $this->send();
    }

    // Use a dedicated method with no dependency for easy testing
    public function send($exceptionCallback = null)
    {
        $logger = $this->campaign->logger();
        $email = $this->subscriber->getEmail();

        try {
            // Prepare the email message to send
            // In case of an invalid email, an exception will arise at: Swift_Mime_SimpleMessage->setTo(...)
            list($message, $msgId) = $this->campaign->prepareEmail($this->subscriber, $this->server, $fromCache = true);

            // Start sending
            $logger->info(sprintf('Sending to %s [Server "%s"]', $email, $this->server->name));


            // Rate limit trackers
            // Here we have 2 rate trackers
            // 1. Sending server sending rate tracker with 1 or more limits.
            // 2. Subscription (plan) sending speed limits with 1 or more limits.
            $rateTrackers = [
                $this->server->getRateLimitTracker(),
            ];

            $creditTrackers = [];

            if (!is_null($this->subscription)) {
                $rateTrackers[] = $this->subscription->getSendEmailRateTracker();

                // @important: right now, do not care about CREDIT
                // $creditTrackers[] = $this->subscription->getSendEmailCreditTracker();
            }

            execute_with_limits($rateTrackers, $creditTrackers, function () use ($message, $logger, $msgId, $email) {
                // Actually send (or throw an exception)
                if (config('custom.dryrun')) {
                    $sent = $this->server->dryrun($message);
                } else {
                    $sent = $this->server->send($message);
                }

                // Log successful shot
                $this->campaign->trackMessage($sent, $this->subscriber, $this->server, $msgId, $this->triggerId);
                $logger->info(sprintf('Sent to %s [Server "%s"]', $email, $this->server->name));
            });
        } catch (RateLimitExceeded $ex) {
            if (!is_null($exceptionCallback)) {
                return $exceptionCallback($ex);
            }
            // Releease the job, have it tried again later on, after 1 minutes
            $logger->warning(sprintf("Delay [%s] for 60 seconds: %s", $email, $ex->getMessage()));

            // Release the job, have it try again after 60 seconds
            // and (hopefully) the quota limits will be lifted then as time goes by
            $this->release(60);
        } catch (OutOfCredits | Throwable $ex) {
            // Also catch the OutOfCredits error
            if (!is_null($exceptionCallback)) {
                return $exceptionCallback($ex);
            }

            $message = sprintf("Error sending to [%s]. Error: %s", $email, $ex->getMessage());
            $logger->error($message);

            // There are 2 options here
            // Option 1: throw an exception and show it to users as the campaign status
            //     throw new Exception($message);
            // Option 2: just skip the error, log it and proceed with the next subscriber

            if ($this->stopOnError) {
                throw $ex;
            } else {
                if (!isset($msgId)) {
                    // Just in case there is an exception before the execution of "list($message, $msgId) = $this->campaign->prepareEmail..."
                    // then $msgID is not available
                    $msgId = null;
                }

                $this->campaign->trackMessage(['status' => 'failed', 'error' => $ex->getMessage()], $this->subscriber, $this->server, $msgId, $this->triggerId);
            }
        }
    }
}
