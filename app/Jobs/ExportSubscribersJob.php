<?php

namespace Acelle\Jobs;

use Acelle\Library\Traits\Trackable;
use Exception;

class ExportSubscribersJob extends Base
{
    use Trackable;

    public $timeout = 3600;

    protected $mailList;
    protected $segment;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($mailList, $segment = null)
    {
        $this->mailList = $mailList;
        $this->segment = $segment;

        // Set the initial value for progress check
        $this->afterDispatched(function ($thisJob, $monitor) {
            $monitor->setJsonData([
                'percentage' => 0,
                'total' => 0,
                'processed' => 0,
                'failed' => 0,
                'message' => trans('messages.list.export.queued.msg'),
                'filepath' => $this->mailList->getExportFilePath(),
            ]);
        });
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        \App::setLocale($this->mailList->customer->language->code);
        \Carbon\Carbon::setLocale($this->mailList->customer->language->code);

        $this->mailList->export(function ($processed, $total, $failed, $message) {
            $percentage = ($total && $processed) ? (int)($processed * 100 / $total) : 0;

            if ($total === 0) {
                $percentage = 100;
            }

            $this->monitor->updateJsonData([
                'percentage' => $percentage,
                'total' => $total,
                'processed' => $processed,
                'failed' => $failed,
                'message' => $message,
            ]);
        }, $this->segment);
    }
}
