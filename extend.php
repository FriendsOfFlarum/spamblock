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

namespace FoF\Spamblock;

use Flarum\Extend;

return [
    (new Extend\Frontend('forum'))
        ->js(__DIR__.'/js/dist/forum.js'),
    new Extend\Locales(__DIR__ . '/locale'),

    (new Extend\Routes('api'))
        ->post('/users/{id}/spamblock', 'users.spamblock', Controllers\MarkAsSpammerController::class),
];
