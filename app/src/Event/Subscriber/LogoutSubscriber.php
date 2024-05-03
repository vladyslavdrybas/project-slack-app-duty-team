<?php

declare(strict_types=1);

namespace App\Event\Subscriber;

use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use Gesdinet\JWTRefreshTokenBundle\Request\Extractor\ExtractorInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTDecodedEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\TokenExtractorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class LogoutSubscriber implements EventSubscriberInterface
{
    public function __construct(
        protected readonly RefreshTokenManagerInterface $refreshTokenManager,
        protected readonly ExtractorInterface $refreshTokenExtractor,
        protected readonly TokenExtractorInterface $tokenExtractor,
        protected readonly JWTTokenManagerInterface $JWTToken,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LogoutEvent::class => 'onLogout',
            Events::JWT_DECODED => 'jwtDecoded',
        ];
    }

    public function jwtDecoded(JWTDecodedEvent $event): void
    {
        //TODO on each request check if user was logout an restrict access
        // it should remove jwt access token immediately. server will not wait for it's expiration.
        // result -> better security. less performance. db load increase.
    }

    public function onLogout(LogoutEvent $event): void
    {
        // get the security token of the session that is about to be logged out
        $token = $event->getToken();
        if (null === $token) {
            return;
        }

        // configure a custom logout response
        $response = new JsonResponse(
            [
                'message' => 'success',
            ],
            Response::HTTP_OK
        );

        $event->setResponse($response);
    }
}
