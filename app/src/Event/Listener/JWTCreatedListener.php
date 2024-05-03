<?php

declare(strict_types=1);

namespace App\Event\Listener;

use App\Entity\User;
use Exception;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class JWTCreatedListener
{
    public function __construct(
        protected readonly RequestStack $requestStack,
        protected readonly TokenStorageInterface $token
    ) {}

    /**
     * @param JWTCreatedEvent $event
     *
     * @return void
     */
    public function onJWTCreated(JWTCreatedEvent $event): void
    {
        if (null === $this->token->getToken()) {
            throw new Exception('Undefined token to set jwt data.');
        }

        $user = $this->token->getToken()->getUser();

        $payload = $event->getData();

        unset($payload['email']);
        unset($payload['roles']);

        if ($user instanceof User) {
            $payload['username'] = $user->getUsername();
            $payload['id'] = $user->getRawId();
        }

        $event->setData($payload);
    }
}
