<?php

namespace Acelle\Library\Contracts;

interface HasTemplateInterface
{
    public function isStageExcluded(string $name): bool;
}
