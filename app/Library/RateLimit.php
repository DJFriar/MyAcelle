<?php

namespace Acelle\Library;

class RateLimit
{
    protected $amount;
    protected $periodValue;
    protected $periodUnit;
    protected $description;

    public const UNLIMITED = -1;

    public function __construct(int $amount, int $periodValue, string $periodUnit, $description = null)
    {
        $this->amount = $amount;
        $this->periodValue = $periodValue;
        $this->periodUnit = $periodUnit;
        $this->description = $description;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getPeriodValue(): int
    {
        return $this->periodValue;
    }

    public function getPeriodUnit(): string
    {
        return $this->periodUnit;
    }

    public function getDescription()
    {
        return $this->description;
    }
}
