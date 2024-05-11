<?php

declare(strict_types=1);

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Monolog\Attribute\WithMonologChannel;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[WithMonologChannel('slack_input')]
#[Route(name: "app")]
class MainController extends AbstractController
{
    #[Route("/", name: "_homepage", methods: ["GET", "OPTIONS", "HEAD"])]
    public function index(): JsonResponse
    {
        return $this->json(["message" => "welcome"]);
    }

    #[Route('/check', name: '_check', methods: ["GET"])]
    public function check(): Response
    {
        return new Response('OK', Response::HTTP_OK);
    }

    #[Route('/slack', name: '_slack', methods: ["GET", "POST"])]
    public function slack(
        Request $request,
        LoggerInterface $slackInputLogger
    ): JsonResponse {
        $slackInputLogger->info('slack request',[$request->getPathInfo(), $request->getPayload()->all()]);

        return $this->json([
            'challenge' => $request->getPayload()->get('challenge')
        ]);
    }
}
