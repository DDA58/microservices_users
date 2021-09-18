<?php

declare(strict_types=1);

namespace App\Subscribers;

use App\Events\UserProfileUpdated;
use App\Jobs\ProduceUserProfileUpdatedJob;
use App\Services\JobDispatcher\IJobDispatcher as JobDispatcher;

final class UserProfileUpdatedSubscriber
{
    private JobDispatcher $jobDispatcher;

    public function __construct(JobDispatcher $jobDispatcher)
    {
        $this->jobDispatcher = $jobDispatcher;
    }

    public function handle(UserProfileUpdated $event): void
    {
        $this->jobDispatcher->dispatch(
            new ProduceUserProfileUpdatedJob($event->getUser()->toArray())
        );
    }
}
