<?php

namespace Acelle\Library\HtmlHandler;

use League\Pipeline\StageInterface;
use Acelle\Library\StringHelper;

class InjectMessageIdToBody implements StageInterface
{
    public $msgId;

    public function __construct($msgId)
    {
        $this->msgId = $msgId;
    }

    public function __invoke($html)
    {
        // Inject the message ID to email body
        // It is used to track reply
        $msgId = $this->msgId ?? '[ null ]';
        $pixel = sprintf('<img src="%s" data="X-Client-Message-Id: %s" alt="X-Client-Message-Id: %s" width="0" height="0" alt="" style="visibility:hidden" />', asset('images/transparent.gif'), $msgId, $msgId);
        return StringHelper::appendHtml($html, $pixel);
    }
}
