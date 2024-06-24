<?php

namespace Acelle\Library;

use Carbon\Carbon;
use Exception;
use Acelle\Library\Exception\RateLimitExceeded;
use Closure;

/*
 * Log every credit used to a log file, group by $mode, which could be 'minute' (default), hour, day, month...
 * For example, a log file grouped by minute may look like this ( lines of YYYYMMDDHHMM:COUNT format)
 *
 * 202307230913:1200
 * 202307230914:1250
 * 202307230914:1052
 * 202307230930:1178
 *
 * Group by 'minute' supports minute granularity limits (emails per 1 minute)
 * Group by 'hour' supports hour granularity limits (emails per 1 hour), but does not support minute (lower than 1 hour) granularity limits
 *
 */

class RateTracker
{
    protected $filepath;
    protected $mode = 'minute'; // hour, day, month, year
    protected $seperator = ':';
    protected $blockFormat = [
        'minute' => 'YmdHi',
        'hour' => 'YmdH00',
        'day' => 'Ymd0000',
        'month' => 'Ym000000',
        'year' => 'Y00000000',
    ];

    protected $limits;

    public function __construct(string $filepath, $limits = []) // RateLimit class
    {
        $this->filepath = $filepath;
        $this->limits = $limits;
        $this->createStorageFile();
    }

    public function count(Carbon $now = null)
    {
        $lock = new Lockable($this->filepath);
        $lock->getExclusiveLock(function ($fopen) use ($now) {
            $now = $now ?: Carbon::now();

            // Throw an exception if test fails (quota exceeded)
            $this->test($now, $fopen);

            // Record credits use
            $this->record($now, $fopen);
        }, $timeout = 15);
    }

    // Reverse of count()
    // @deprecated: rollback is not needed as even a failed operation is also counted in rate limits
    public function rollback()
    {
        $lock = new Lockable($this->filepath);
        $lock->getExclusiveLock(function ($fopen) {
            list($lastBlock, $count) = $this->parseLastRecord($fopen);
            if (is_null($lastBlock)) {
                throw new Exception('Cannot rollback! There is no previous count (hey, what if file was cleaned up while SendMessage is in progress');
            }

            if ($count == 1) {
                $this->removeLastRecord($fopen);
            } else {
                $record = $this->buildRecord($lastBlock, $count - 1);
                $this->updateRecord($record, $fopen);
            }
        }, $timeout = 15);
    }

    private function createStorageFile()
    {
        if (!file_exists($this->filepath)) {
            touch($this->filepath);
        }
    }

    private function test(Carbon $now, $fopen)
    {
        foreach ($this->limits as $limit) {
            $period = sprintf("%s %s", $limit->getPeriodValue(), $limit->getPeriodUnit());
            $fromDatetime = $now->copy()->subtract($period);

            $creditsUsed = $this->getCreditsUsed($fromDatetime, $now, $fopen);

            if ($creditsUsed >= $limit->getAmount()) {
                throw new RateLimitExceeded(sprintf("%s exceeded! %s/%s used", $limit->getDescription(), $creditsUsed, $limit->getAmount()));
            }
        }
    }

    private function record(Carbon $now, $fopen)
    {
        // Make something like: 202307231527
        $currentBlock = $this->makeBlock($now); // create block for the current date/time
        list($lastBlock, $count) = $this->parseLastRecord($fopen);

        // EMPTY() is safer than IS_NULL()
        if ($currentBlock == $lastBlock) {
            $record = $this->buildRecord($lastBlock, $count + 1);
            $this->updateRecord($record, $fopen);
        } else {
            $record = $this->buildRecord($currentBlock, $count = 1);
            $this->addRecord($record, $fopen);
        }
    }

    private function parseLastRecord($fopen)
    {
        $lastRecord = $this->getLastRecord($fopen);

        // Return something like: ['202307230913', 120]
        return $this->parseBlock($lastRecord);
    }

    private function parseBlock(string $record)
    {
        if (empty($record)) {
            return [$lastBlock = null, $count = null];
        }

        // Return something like: ['202307230913', 120]
        return explode($this->seperator, $record);
    }

    public function buildRecord($block, $count)
    {
        return "{$block}{$this->seperator}{$count}";
    }

    // Convert the provided datetime $now to a string
    public function makeBlock($now)
    {
        $now = $now ?: Carbon::now();
        $format = $this->blockFormat[$this->mode];
        return $now->format($format);
    }

    // Return string like: 202307230913:120
    private function getLastRecord($fopen)
    {
        // Find offline
        fseek($fopen, 0, SEEK_END);
        $offset = ftell($fopen) - 1; // Offset values from: -1, 0, 1, 2...

        if ($offset < 0) {
            return ""; // File empty
        }

        fseek($fopen, $offset--); //seek to the end of the line

        // Ignore consecutive empty newlines
        $char = fgetc($fopen);
        while ($offset >= 0 && ($char === "\n")) {
            fseek($fopen, $offset--);
            $char = fgetc($fopen);
        }

        if ($offset < 0) {
            fseek($fopen, 0);
            return trim(fgets($fopen)); // the whole file has Zero or One character (except \n)
        }

        // Continue with offset $offset;
        fseek($fopen, $offset--);
        $char = fgetc($fopen);
        while ($offset >= 0 && $char != "\n") {
            fseek($fopen, $offset--);
            $char = fgetc($fopen);
        }

        if ($offset < 0) { // get to the beginning of file
            fseek($fopen, 0);
        }

        $lastLine = fgets($fopen);
        return trim($lastLine);
    }

    public function updateRecord(string $record, $fopen)
    {
        // Move cursor to the end of file
        fseek($fopen, 0, SEEK_END);

        // Returns the current position of the file read/write pointer
        $offset = ftell($fopen) - 1; // Offset values from: -1, 0, 1, 2...

        if ($offset < 0) {
            return ""; // File empty
        }

        fseek($fopen, $offset--); //seek to the end of the line

        // Ignore consecutive empty newlines
        $char = fgetc($fopen);
        while ($offset >= 0 && ($char === "\n")) {
            fseek($fopen, $offset--);
            $char = fgetc($fopen);
        }

        if ($offset < 0) {
            fseek($fopen, 0); // either a leading newline or leading newline + 1char, overwrite leading "\nX" if any
        }

        // Continue with offset $offset;
        fseek($fopen, $offset);
        $char = fgetc($fopen);
        while ($offset >= 0 && $char != "\n") {
            $offset -= 1;
            fseek($fopen, $offset);
            $char = fgetc($fopen);
        }

        if ($offset < 0) { // get to the beginning of file
            fseek($fopen, 0);
        }

        fwrite($fopen, $record);
    }

    public function removeLastRecord($fopen)
    {
        fseek($fopen, 0, SEEK_END);
        $offset = ftell($fopen) - 1; // Offset values from: -1, 0, 1, 2...

        if ($offset < 0) {
            return; // File empty
        }

        fseek($fopen, $offset--); //seek to the end of the line

        // Ignore consecutive empty newlines
        $char = fgetc($fopen);
        while ($offset >= 0 && ($char === "\n")) {
            fseek($fopen, $offset--);
            $char = fgetc($fopen);
        }

        if ($offset < 0) {
            fseek($fopen, 0); // either a leading newline or leading newline + 1char, overwrite leading "\nX" if any
        }

        fseek($fopen, $offset); //seek to the end of the line
        $char = fgetc($fopen);
        while ($offset >= 0 && $char != "\n") {
            $offset -= 1;
            fseek($fopen, $offset);
            $char = fgetc($fopen);
        }

        ftruncate($fopen, ++$offset);
    }

    public function addRecord(string $record, $fopen)
    {
        fseek($fopen, 0, SEEK_END);
        $offset = ftell($fopen) - 1; // Offset values from: -1, 0, 1, 2...

        if ($offset < 0) {
            fwrite($fopen, $record);
        } else {
            fseek($fopen, $offset); //seek to the end of the line
            $char = fgetc($fopen);
            while ($offset > 0 && ($char === "\n")) {
                $offset -= 1;
                fseek($fopen, $offset);
                $char = fgetc($fopen);
            }

            fseek($fopen, ++$offset);
            fwrite($fopen, "\n".$record);
        }
    }

    public function getRecords(Carbon $fromDatetime = null, Carbon $toDatetime = null, $fopen = null)
    {
        $fromDatetime = $fromDatetime ?: Carbon::createFromTimestamp(0); // Create the earliest date of 1970-01-01
        $toDatetime = $toDatetime ?: Carbon::now(); // Current date

        $fromDatetimeStr = $this->makeBlock($fromDatetime);
        $toDatetimeStr = $this->makeBlock($toDatetime);

        $records = [];

        if (is_null($fopen)) {
            $fopen = fopen($this->filepath, 'r');
            $closeFile = true;
        } else {
            rewind($fopen);
            $closeFile = false;
        }

        rewind($fopen);
        while (!feof($fopen)) {
            $record = trim(fgets($fopen));

            if (empty($record)) {
                break;
            }

            list($block, $count) = $this->parseBlock($record);

            if (empty($block)) {
                throw new Exception("Invalid block {$record}");
            }

            if ($block >= $fromDatetimeStr && $block <= $toDatetimeStr) {
                $records[] = [$block, $count];
            }
        }

        if ($closeFile) {
            fclose($fopen);
        }

        // Return
        return $records;
    }

    public function getCreditsUsed(Carbon $fromDatetime = null, Carbon $toDatetime = null, $fopen = null)
    {
        $records = $this->getRecords($fromDatetime, $toDatetime, $fopen);
        $counts = array_map(function ($record) {
            list($block, $count) = $record;
            return $count;
        }, $records);

        $total = array_sum($counts);
        return $total;
    }

    public function getLockFilePath()
    {
        return $this->filepath;
    }

    public function getRateLimits()
    {
        return $this->limits;
    }
}
