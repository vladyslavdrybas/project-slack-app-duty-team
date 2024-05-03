<?php

declare(strict_types=1);

namespace App\Event\Subscriber;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTExpiredEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;

class ExceptionJsonSubscriber implements EventSubscriberInterface
{
    protected string $environment;

    public function __construct(
        protected readonly string $projectEnvironment,
        protected readonly ParameterBagInterface $parameterBag
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            // the priority must be greater than the Security HTTP
            // ExceptionListener, to make sure it's called before
            // the default exception listener
            KernelEvents::EXCEPTION => ['onKernelException', 100],
            Events::JWT_EXPIRED => 'jwtExpired',
        ];
    }

    public function jwtExpired(JWTExpiredEvent $event): void
    {
        $data = [
            'status' => Response::HTTP_UNAUTHORIZED,
            'environment' => $this->projectEnvironment,
            'service' => $this->parameterBag->get('service_name'),
            'version' => $this->parameterBag->get('api_version'),
            'message' => 'JWT expired.',
        ];

        $event->setResponse(new JsonResponse($data, Response::HTTP_UNAUTHORIZED));
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $code = Response::HTTP_BAD_REQUEST;
        $message = $exception->getMessage();
        if ($exception instanceof AccessDeniedException) {
            $code = Response::HTTP_UNAUTHORIZED;
            $message = 'Access denied';
        } else if ($exception instanceof NotFoundHttpException) {
            $code = Response::HTTP_NOT_FOUND;
            $message = '404 not found';
        } else if ($exception instanceof MethodNotAllowedException) {
            $code = Response::HTTP_METHOD_NOT_ALLOWED;
            $message = 'Method not allowed';
        }

        $data = [
            'status' => $code,
            'host' => $event->getRequest()->server->get('HOST'),
            'requestUri' => $event->getRequest()->server->get('REQUEST_URI'),
            'queryString' => $event->getRequest()->server->get('QUERY_STRING'),
            'environment' => $this->projectEnvironment,
            'service' => $this->parameterBag->get('service_name'),
            'version' => $this->parameterBag->get('api_version'),
            'message' => $message,
        ];

        if ($this->projectEnvironment !== 'prod') {
            $data['trace'] = $exception->getTrace();
        }

        $event->setResponse(new JsonResponse($data,$code));
        // or stop propagation (prevents the next exception listeners from being called)
        //$event->stopPropagation();
    }
}
