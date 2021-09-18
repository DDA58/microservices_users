<?php

declare(strict_types=1);

namespace App\Services\JobDispatcher;

use App\Jobs\ABaseJob;
use App\Models\FailingsBrokerJob;
use Illuminate\Contracts\Bus\Dispatcher;
use PhpAmqpLib\Exception\AMQPIOException;

final class LaravelJobDispatcher implements IJobDispatcher
{
    private Dispatcher $dispatcher;

    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function dispatch(ABaseJob $job): void
    {
        try {
            $this->dispatcher->dispatch($job);
        } catch(AMQPIOException $e) {
            $fail = new FailingsBrokerJob();
            $fail->priority = $job->getFailingPriority();
            $fail->serialized_object = serialize($job);
            $fail->exception = $e->getMessage();
            $fail->save();
        }
    }
}
