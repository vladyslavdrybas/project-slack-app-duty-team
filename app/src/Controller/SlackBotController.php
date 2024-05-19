<?php

namespace App\Controller;

use App\Services\DutyTeamSlackBot\CommandPreProcessor;
use App\Services\DutyTeamSlackBot\DataTransferObject\Command\SlackCommandInputDto;
use App\Services\DutyTeamSlackBot\DataTransferObject\Transformer\SlackCommandTransformer;
use App\Services\DutyTeamSlackBot\CommandProcessor;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
        $slackInputLogger->info('slack message request',[$request->getPathInfo(), $request->getPayload()->all()]);

        return $this->json([
            'challenge' => $request->getPayload()->get('challenge')
        ]);
    }

    #[Route('/command', name: '_command', methods: ["POST", "PUT"])]
    public function command(
        Request                 $request,
        SlackCommandTransformer $transformer,
        CommandPreProcessor     $commandPreProcessor,
        CommandProcessor        $commandProcessor,
        LoggerInterface         $slackInputLogger
    ): Response {
        try {
            $payload =  $request->getPayload()->all();
            $slackInputLogger->debug('slack command request',[$request->getPathInfo(), $request->getPayload()->all()]);

            $slackCommandInputDto = $this->serializer->denormalize($payload, SlackCommandInputDto::class);
            $slackInputLogger->debug('slack command input dto', [$slackCommandInputDto]);

            $dto = $transformer->transform($slackCommandInputDto);
            $slackInputLogger->debug('slack command dto', [$dto]);

            $slackCommand = $commandPreProcessor->process($dto);
            $slackInputLogger->debug('slack command', [$slackCommand]);

            $answer = $commandProcessor->process($slackCommand);
            $slackInputLogger->debug('slack command answer', [$answer->text]);
        } catch (\Exception $e) {
            $slackInputLogger->error($e->getMessage());

            throw $e;
        }

        return new Response($answer->text, $answer->code);
    }
}
