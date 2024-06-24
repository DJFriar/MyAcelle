<?php

namespace Acelle\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

use Acelle\Model\MailList;

class MailListImported
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public $list;
    public $importBatchId;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(MailList $list, $importBatchId)
    {
        $this->list = $list;
        $this->importBatchId = $importBatchId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
