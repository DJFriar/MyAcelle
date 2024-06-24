<?php

namespace Acelle\Library;

use Illuminate\Database\Eloquent\Model;
use Acelle\Library\Traits\HasUid;
use Acelle\Library\Traits\HasCache;
use Acelle\Library\Traits\TrackJobs;
use Acelle\Library\Lockable;
use Carbon\Carbon;
use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Formatter\LineFormatter;
use Acelle\Jobs\LoadCampaign;
use Acelle\Jobs\RunCampaign;
use Illuminate\Bus\Batch;
use Acelle\Events\CampaignUpdated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Throwable;

class BaseCampaign extends Model
{
    use TrackJobs;
    use HasUid;
    use HasCache;
    use HasFactory;

    protected $logger;

    // Campaign status
    public const STATUS_NEW = 'new';
    public const STATUS_QUEUING = 'queuing'; // equiv. to 'queue'
    public const STATUS_QUEUED = 'queued'; // equiv. to 'queue'
    public const STATUS_SENDING = 'sending';
    public const STATUS_ERROR = 'error';
    public const STATUS_DONE = 'done';
    public const STATUS_PAUSED = 'paused';
    public const STATUS_SCHEDULED = 'scheduled';

    /**
     * Associations
     */
    public function customer()
    {
        return $this->belongsTo('Acelle\Model\Customer');
    }

    /**
     * Scope
     */
    public function scopeScheduled($query)
    {
        return $query->where('status', static::STATUS_SCHEDULED);
    }

    public function setDone()
    {
        $this->status = self::STATUS_DONE;
        $this->last_error = null;
        $this->save();
    }

    public function setSending()
    {
        $this->status = self::STATUS_SENDING;
        $this->running_pid = getmypid();
        $this->delivery_at = Carbon::now();
        $this->save();
    }

    public function isSending()
    {
        return $this->status == self::STATUS_SENDING;
    }

    public function isDone()
    {
        return $this->status == self::STATUS_DONE;
    }

    public function isQueued()
    {
        return $this->status == self::STATUS_QUEUED;
    }

    public function setQueued()
    {
        $this->status = self::STATUS_QUEUED;
        $this->save();
        return $this;
    }

    public function setQueuing()
    {
        $this->status = self::STATUS_QUEUING;
        $this->save();
        return $this;
    }

    public function setError($error = null)
    {
        $this->status = self::STATUS_ERROR;
        $this->last_error = $error;
        $this->save();
        return $this;
    }

    public static function checkAndExecuteScheduledCampaigns()
    {
        $lockFile = storage_path('tmp/check-and-execute-scheduled-campaign');
        $lock = new Lockable($lockFile);
        $timeout = 5; // seconds
        $timeoutCallback = function () {
            // pass this to the getExclusiveLock method
            // to have it silently quit, without throwing an exception
            return;
        };

        $lock->getExclusiveLock(function ($f) {
            foreach (static::scheduled()->get() as $campaign) {
                $campaign->execute();
            }
        }, $timeout, $timeoutCallback);
    }

    public function deleteAndCleanup()
    {
        if ($this->template) {
            $this->template->deleteAndCleanup();
        }

        $this->cancelAndDeleteJobs();

        $this->delete();
    }

    public function isError()
    {
        return $this->status == self::STATUS_ERROR;
    }

    // This method called when user clicks CONFIRM button on the web UI
    // This method is also called by a cronjob which periodically check for campaigns to run
    public function execute()
    {
        $now = Carbon::now();

        if (!is_null($this->run_at) && $this->run_at->gte($now)) {
            $scheduledAt = $this->run_at->timezone($this->customer->timezone);
            $this->logger()->warning(sprintf('Campaign is scheduled at %s (%s)', $scheduledAt->format('Y-m-d H:m'), $scheduledAt->diffForHumans()));
            return;
        }

        // Delete previous RunCampaign jobs
        $this->cancelAndDeleteJobs(RunCampaign::class);

        // Schedule Job initialize
        $job = (new RunCampaign($this));

        // Dispatch using the method provided by TrackJobs
        // to also generate job-monitor record
        $this->dispatchWithMonitor($job);

        // After this job is dispatched successfully, set status to "queuing"
        // Notice the different between the two statuses
        // + Queuing: waiting until campaign is ready to run
        // + Queued: ready to run
        $this->setQueuing();
    }

    public function setScheduled()
    {
        $this->status = self::STATUS_SCHEDULED;
        $this->save();
        return $this;
    }

    public function resume()
    {
        $this->execute();
    }

    // Should be called by RunCampaign
    public function run()
    {
        // Pause any previous batch no matter what status it is
        // Notice that batches without a job_monitor will not be retrieved
        $jobs = $this->jobMonitors()->byJobType(LoadCampaign::class)->get();
        foreach ($jobs as $job) {
            $job->cancelWithoutDeleteBatch();
        }

        // Campaign loader job
        $campaignLoader = new LoadCampaign($this);

        // Dispatch it with a batch monitor
        $this->dispatchWithBatchMonitor(
            $campaignLoader,
            function ($batch) {
                // THEN callback of a batch
                //
                // Important:
                // Notice that if user manually cancels a batch, it still reaches trigger "then" callback!!!!
                // Only when an exception is thrown, no "then" trigger
                // @Update: the above statement is longer true! Cancelling a batch DOES NOT trigger "THEN" callback
                //
                // IMPORTANT: refresh() is required!
                if (!$this->refresh()->isPaused()) {
                    $count = $this->subscribersToSend()->count();
                    if ($count > 0) {
                        // Run over and over again until there is no subscribers left to send
                        // Because each LoadCampaign jobs only load a fixed number of subscribers
                        $this->updateCache();
                        $this->logger()->warning('Load another batch of '.$count);
                        $this->run();
                    } else {
                        $this->logger()->warning('No contact left, campaign finishes successfully!');
                        $this->setDone();
                    }
                } else {
                    // do nothing, as campaign is already PAUSED by user (not by an exception)
                    $this->logger()->warning('Campaign is paused by user');
                }
            },
            function (Batch $batch, Throwable $e) {
                // CATCH callback
                $errorMsg = "Campaign stopped. ".$e->getMessage()."\n".$e->getTraceAsString();
                $this->logger()->info($errorMsg);
                $this->setError($errorMsg);
            },
            function () {
                // FINALLY callback
                $this->logger()->info('Finally!');
                $this->updateCache();
            }
        );

        // SET QUEUED
        $this->setQueued();

        /**** MORE NOTES ****/
        //
        // Important: in case one of the batch's jobs hits an error
        // the batch is automatically set to cancelled and, therefore, all remaining jobs will just finish (return)
        // resulting in the "finally" event to be triggered
        // So, do not update satus here, otherwise it will overwrite any status logged by "catch" event
        // Notice that: if a batch fails (automatically canceled due to one failed job)
        // then, after all jobs finishes (return), [failed job] = [pending job] = 1
        // +------------+--------------+-------------+---------------------------------------------------------------------------------+-------------+
        // | total_jobs | pending_jobs | failed_jobs | failed_job_ids                                                                  | finished_at |
        // +------------+--------------+-------------+---------------------------------------------------------------------------------+-------------+
        // |          7 |            0 |           0 | []                                                                              |  1624848887 | success
        // |          7 |            1 |           1 | ["302130fd-ba78-4a37-8a3b-2304cc3f3455"]                                        |  1624849156 | failed
        // |          7 |            2 |           2 | ["6a17f9bf-96d4-48e5-86a0-73e7bac07e74","7e1b3b3d-a5f4-45b4-be1e-ba5f1cc2e3f3"] |  1624849222 | (*)
        // |          7 |            3 |           2 | ["6a17f9bf-96d4-48e5-86a0-73e7bac07e74","7e1b3b3d-a5f4-45b4-be1e-ba5f1cc2e3f3"] |  1624849222 | (**)
        // |          7 |            2 |           0 | []                                                                              |        NULL | (***)
        // +------------+--------------+-------------+---------------------------------------------------------------------------------+-------------+
        //
        // (*) There is no batch cancelation check in every job
        // as a result, remaining jobs still execute even after the batch is automatically cancelled (due to one failed job)
        // resulting in 2 (or more) failed / pending jobs
        //
        // (**) 2 jobs already failed, there is 1 remaining job to finish (so 3 pending jobs)
        // That is, pending_jobs = failed jobs + remaining jobs
        //
        // (***) If certain jobs are deleted from queue or terminated during action (without failing or finishing)
        // Then the campaign batch does not reach "then" status
        // Then proceed with pause and send again
    }

    public function logger()
    {
        if (!is_null($this->logger)) {
            return $this->logger;
        }

        $formatter = new LineFormatter("[%datetime%] %channel%.%level_name%: %message%\n");

        $logfile = $this->getLogFile();
        $stream = new RotatingFileHandler($logfile, 0, Logger::DEBUG);
        $stream->setFormatter($formatter);

        $pid = getmypid();
        $logger = new Logger($pid);
        $logger->pushHandler($stream);
        $this->logger = $logger;

        return $this->logger;
    }

    public function getLogFile()
    {
        $path = storage_path(join_paths('logs', php_sapi_name(), '/campaign-'.$this->uid.'.log'));
        return $path;
    }

    public function extractErrorMessage()
    {
        return explode("\n", $this->last_error)[0];
    }

    public function scheduleDiffForHumans()
    {
        if ($this->run_at) {
            return $this->run_at->timezone($this->customer->timezone)->diffForHumans();
        } else {
            return null;
        }
    }

    public function pause()
    {
        $this->cancelAndDeleteJobs();
        $this->setPaused();

        // Update status
        event(new CampaignUpdated($this));
    }

    public function setPaused()
    {
        // set campaign status
        $this->status = self::STATUS_PAUSED;
        $this->save();
        return $this;
    }

    public function isPaused()
    {
        return $this->status == self::STATUS_PAUSED;
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public static function statusSelectOptions()
    {
        return [
            ['text' => trans('messages.campaign_status_' . self::STATUS_NEW), 'value' => self::STATUS_NEW],
            ['text' => trans('messages.campaign_status_' . self::STATUS_QUEUING), 'value' => self::STATUS_QUEUING],
            ['text' => trans('messages.campaign_status_' . self::STATUS_QUEUED), 'value' => self::STATUS_QUEUED],
            ['text' => trans('messages.campaign_status_' . self::STATUS_SENDING), 'value' => self::STATUS_SENDING],
            ['text' => trans('messages.campaign_status_' . self::STATUS_ERROR), 'value' => self::STATUS_ERROR],
            ['text' => trans('messages.campaign_status_' . self::STATUS_DONE), 'value' => self::STATUS_DONE],
            ['text' => trans('messages.campaign_status_' . self::STATUS_PAUSED), 'value' => self::STATUS_PAUSED],
            ['text' => trans('messages.campaign_status_' . self::STATUS_SCHEDULED), 'value' => self::STATUS_SCHEDULED],
        ];
    }
}
