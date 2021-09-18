<?php

namespace App\Jobs;

class ProduceUserProfileUpdatedJob extends ABaseJob
{
    public $queue = 'default';

    private array $user;

    /**
     * @param array $user
     */
    public function __construct(array $user)
    {
        $this->user = $user;
    }

    public function handle() {

    }
}
