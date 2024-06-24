<?php

namespace Acelle\Library;

use Acelle\Library\Exception\OutOfCredits;
use Acelle\Library\Lockable;
use Exception;

class CreditTracker
{
    protected $filepath;

    public const ZERO = 0;
    public const UNLIMITED = -1;

    // Using CreditTracker::load('file') makes more sense than
    public static function load($filepath, bool $createFileIfNotExists = false, int $default = self::UNLIMITED)
    {
        $tracker = new self($filepath, $createFileIfNotExists, $default);
        return $tracker;
    }

    private function __construct($filepath, bool $createFileIfNotExists, int $default)
    {
        $this->filepath = $filepath;

        if ($createFileIfNotExists && !file_exists($this->filepath)) {
            $this->createFile();
            $this->setCredits($default);
        }
    }

    public function createFile()
    {
        $file = fopen($this->filepath, 'w');
        fclose($file);
    }

    public function getRemainingCredits()
    {
        $credits = file_get_contents($this->filepath);
        if (empty(trim($credits))) {
            return self::ZERO;
        }

        return (int)$credits;
    }

    private function test()
    {
        if ($this->getRemainingCredits() == self::ZERO) {
            throw new OutOfCredits('Credits exceeded');
        }
    }

    public function count()
    {
        Lockable::withExclusiveLock($this->filepath, function () {
            $this->test();

            $remainingCredits = $this->getRemainingCredits();
            $remainingCredits -= 1;
            $remainingCredits = "{$remainingCredits}"; // cast to string

            file_put_contents($this->filepath, $remainingCredits);
        });
    }

    public function setCredits($amount)
    {
        if (!is_int($amount)) {
            throw new Exception('Invalid value for CreditTracker credits. Try using "(int)$credit": '.$amount);
        }

        if ($amount < self::UNLIMITED) {
            throw new Exception('Invalid value for CreditTracker credits (Integer >= -1): '.$amount);
        }

        file_put_contents($this->filepath, (string)$amount);
        return $this;
    }

    public function rollback()
    {
        $remainingCredits = $this->getRemainingCredits();
        $remainingCredits += 1;
        $remainingCredits = "{$remainingCredits}"; // cast to string

        file_put_contents($this->filepath, $remainingCredits);
    }
}
