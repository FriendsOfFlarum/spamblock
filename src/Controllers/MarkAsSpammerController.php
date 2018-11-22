<?php

namespace FoF\Spamblock\Controllers;

use Carbon\Carbon;
use Flarum\Extension\ExtensionManager;
use Flarum\User\User;
use Zend\Diactoros\Response;
use Flarum\Post\Command\EditPost;
use Flarum\User\Command\EditUser;
use Flarum\Post\Command\DeletePost;
use Flarum\User\AssertPermissionTrait;
use Psr\Http\Message\ResponseInterface;
use Illuminate\Contracts\Bus\Dispatcher;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Flarum\Discussion\Command\EditDiscussion;
use Flarum\Discussion\Command\DeleteDiscussion;

class MarkAsSpammerController implements RequestHandlerInterface
{
    use AssertPermissionTrait;

    /**
     * @var Dispatcher
     */
    protected $bus;

    /**
     * @var ExtensionManager
     */
    protected $extensions;

    /**
     * @param Dispatcher $bus
     * @param ExtensionManager $extensions
     */
    public function __construct(Dispatcher $bus, ExtensionManager $extensions)
    {
        $this->bus = $bus;
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

        $this->assertAdmin($actor);

        $userId = array_get($request->getQueryParams(), 'id');
        $user = User::findOrFail($userId);

        if ($this->extensions->isEnabled('flarum-suspend') && !isset($user->suspended_until)) {
            $this->bus->dispatch(
                new EditUser($user->id, $actor, [
                    'attributes' => ['suspendedUntil' => Carbon::now()->addYear(20)]
                ])
            );
        }


        foreach ($user->posts as $post) {
            if ($post->is_hidden) continue;

            $this->bus->dispatch(
                new EditPost($post->id, $actor, [
                    'attributes' => ['isHidden' => true]
                ])
            );
        }

        foreach ($user->discussions as $discussion) {
            if ($discussion->is_hidden) continue;

            $this->bus->dispatch(
                new EditDiscussion($discussion->id, $actor, [
                    'attributes' => ['isHidden' => true]
                ])
            );
        }

        return (new Response())->withStatus(204);
    }
}
