<?php

namespace FoF\Spamblock\Listeners;

use Flarum\Api\Event\Serializing;
use Flarum\Api\Serializer\UserSerializer;
use Illuminate\Contracts\Events\Dispatcher;

class AddPermissions
{
    /**
     * @param Dispatcher $events
     */
    public function subscribe(Dispatcher $events)
    {
        $events->listen(Serializing::class, [$this, 'serializing']);
    }

    /**
     * @param Serializing $event
     */
    public function serializing(Serializing $event)
    {
        if ($event->isSerializer(UserSerializer::class)) {
            $event->attributes['canSpamblock'] = $event->actor->can('spamblock', $event->model);
        }
    }
}