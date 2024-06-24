<?php

namespace Acelle\Jobs;

class SendConfirmationEmailJob extends Base
{
    protected $subscribers;
    protected $mailList;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($subscribers, $mailList)
    {
        $this->subscribers = $subscribers;
        $this->mailList = $mailList;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->subscribers as $subscriber) {
            $this->mailList->sendSubscriptionConfirmationEmail($subscriber);
        }
    }
}
