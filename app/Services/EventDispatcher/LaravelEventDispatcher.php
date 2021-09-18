<?php

declare(strict_types=1);

namespace App\Services\EventDispatcher;

use App\Events\IEvent;
use App\Models\FailingsBrokerJob;
use Illuminate\Contracts\Events\Dispatcher;
use PhpAmqpLib\Exception\AMQPIOException;

final class LaravelEventDispatcher implements IEventDispatcher
{
    private Dispatcher $dispatcher;

    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function dispatch(IEvent $event): void
    {
        $this->dispatcher->dispatch($event);
    }
}
