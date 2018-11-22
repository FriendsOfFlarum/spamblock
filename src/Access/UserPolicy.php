<?php

/**
 *  This file is part of fof/spamblock.
 *
 *  Copyright (c) 2018 .
 *
 *
 *  For the full copyright and license information, please view the LICENSE.md
 *  file that was distributed with this source code.
 */

namespace FoF\Spamblock\Access;

use Flarum\User\AbstractPolicy;
use Flarum\User\User;

class UserPolicy extends AbstractPolicy
{
    /**
     * {@inheritdoc}
     */
    protected $model = User::class;

    /**
     * @param User $actor
     * @param User $user
     * @return bool|null
     */
    public function spamblock(User $actor, User $user)
    {
        if ($user->id === $actor->id) {
            return false;
        }
    }
}
