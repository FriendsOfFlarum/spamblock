<?php

namespace FoF\Spamblock\Controllers;

use Carbon\Carbon;
use Flarum\Extension\ExtensionManager;
use Flarum\User\User;
use FoF\Spamblock\Event\MarkedUserAsSpammer;
use Zend\Diactoros\Response;
use Flarum\Post\Command\EditPost;
use Flarum\User\Command\EditUser;
use Flarum\User\AssertPermissionTrait;
use Psr\Http\Message\ResponseInterface;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Contracts\Events\Dispatcher as EventsDispatcher;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Flarum\Discussion\Command\EditDiscussion;

class MarkAsSpammerController implements RequestHandlerInterface
{
    use AssertPermissionTrait;

    /**
     * @var Dispatcher
     */
    protected $bus;

    /**
     * @var EventsDispatcher
     */
    protected $events;

    /**
     * @var ExtensionManager
     */
    protected $extensions;

    /**
     * @param EventsDispatcher $events
     * @param Dispatcher $bus
     * @param ExtensionManager $extensions
     */
    public function __construct(Dispatcher $bus, EventsDispatcher $events, ExtensionManager $extensions)
    {
        $this->bus = $bus;
        $this->events = $events;
        $this->extensions = $extensions;
    }

    /**
     * Handle the request and return a response.
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        $actor = $request->getAttribute('actor');

        $userId = array_get($request->getQueryParams(), 'id');
        $user = User::findOrFail($userId);

        $this->assertCan($actor, 'spamblock', $user);

        if ($this->extensions->isEnabled('flarum-suspend') && !isset($user->suspended_until)) {
            $this->bus->dispatch(
                new EditUser($user->id, $actor, [
                    'attributes' => ['suspendedUntil' => Carbon::now()->addYear(20)]
                ])
            );
        }

        $user->posts()->where('hidden_at', null)->chunk(50, function ($posts) use ($actor) {
            foreach ($posts as $post) {
                $this->bus->dispatch(
                    new EditPost($post->id, $actor, [
                        'attributes' => ['isHidden' => true]
                    ])
                );
            }
        });

        $user->discussions()->where('hidden_at', null)->chunk(50, function ($discussions) use ($actor) {
            foreach ($discussions as $discussion) {
                $this->bus->dispatch(
                    new EditDiscussion($discussion->id, $actor, [
                        'attributes' => ['isHidden' => true]
                    ])
                );
            };
        });

        $this->events->dispatch(
            new MarkedUserAsSpammer($user, $actor)
        );

        return (new Response())->withStatus(204);
    }
}
