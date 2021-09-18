<?php

declare(strict_types=1);

namespace  App\Services\JobDispatcher;

use App\Jobs\ABaseJob;

interface IJobDispatcher
{
    public function dispatch(ABaseJob $job): void;
}
