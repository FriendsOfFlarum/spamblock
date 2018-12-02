<?php

namespace FoF\Spamblock\Event;

use Flarum\User\User;

class MarkedUserAsSpammer {
    /**
     * @var User
     */
    public $user;

    /**
     * @var User
     */
    public $actor;

    public function __construct(User $user, User $actor)
    {
        $this->user = $user;
        $this->actor = $actor;
    }
}
