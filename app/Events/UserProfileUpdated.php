<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Queue\SerializesModels;

class UserProfileUpdated implements IEvent
{
    use SerializesModels;

    /**
     *
     * @var User
     */
    private $user;

    /**
     * Create a new event instance.
     *
     * @param  User  $user
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getUser(): User {
        return $this->user;
    }
}

