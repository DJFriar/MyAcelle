<?php

namespace Acelle\Library\Facades;

use Illuminate\Support\Facades\Facade;
use Acelle\Library\SubscriptionManager;

class SubscriptionFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return SubscriptionManager::class;
    }
}
