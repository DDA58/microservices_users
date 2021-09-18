<?php

namespace App\Services\EventDispatcher;

use App\Events\IEvent;

interface IEventDispatcher
{
    public function dispatch(IEvent $event): void;
}
