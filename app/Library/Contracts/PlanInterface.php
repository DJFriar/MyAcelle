<?php

namespace Acelle\Library\Contracts;

interface PlanInterface
{
    public function isFree();
    public function isActive();
    public function getPrice();
    public function hasTrial();
    public function getFrequencyAmount();
    public function getFrequencyUnit();
    public function getTrialAmount();
    public function getTrialUnit();
}
