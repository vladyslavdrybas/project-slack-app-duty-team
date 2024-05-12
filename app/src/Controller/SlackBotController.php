<?php

namespace App\Controller;

use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[WithMonologChannel('slack_input')]
#[Route('/slack', name: "slack")]
class SlackBotController extends AbstractController
{
    #[Route('/message', name: '_message', methods: ["GET", "POST"])]
    public function message(
        Request $request,
        LoggerInterface $slackInputLogger
    ): JsonResponse {
        $slackInputLogger->info('slack request',[$request->getPathInfo(), $request->getPayload()->all()]);

        return $this->json([
            'challenge' => $request->getPayload()->get('challenge')
        ]);
    }

    #[Route('/skills/add', name: '_command_skills_add', methods: ["GET", "POST"])]
    public function skillsAdd(
        Request $request,
        LoggerInterface $slackInputLogger
    ): JsonResponse {
        $slackInputLogger->info('slack request',[$request->getPathInfo(), $request->getPayload()->all()]);

        return $this->json([
            'command' => 'skills-add'
        ]);
    }
}
