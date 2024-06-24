<?php

namespace Acelle\Library;

class TransactionResult
{
    public const RESULT_DONE = 'done';
    public const RESULT_FAILED = 'failed';
    public const RESULT_PENDING = 'pending';

    public $result;
    public $error;

    public function __construct($result, $error = null)
    {
        $this->result = $result;
        $this->error = $error;
    }

    public function isDone()
    {
        // normally run invoice.fulfill() after that
        return $this->result == self::RESULT_DONE;
    }

    public function isFailed()
    {
        // run invoice.payfailed() after that
        return $this->result == self::RESULT_FAILED;
    }

    public function isPending()
    {
        // Normally for services that immediately returns a result already
        //
        return $this->result == self::RESULT_PENDING;
    }
}
