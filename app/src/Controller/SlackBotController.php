<?php

namespace App\Controller;

use App\Services\DutyTeamSlackBot\CommandPreProcessor;
use App\Services\DutyTeamSlackBot\DataTransferObject\Command\SlackCommandInputDto;
use App\Services\DutyTeamSlackBot\DataTransferObject\Interactivity\SlackInteractivityInputDto;
use App\Services\DutyTeamSlackBot\DataTransferObject\Transformer\SlackCommandTransformer;
use App\Services\DutyTeamSlackBot\CommandProcessor;
use App\Services\DutyTeamSlackBot\DataTransferObject\Transformer\SlackInteractivityTransformer;
use App\Services\DutyTeamSlackBot\InteractivityPreProcessor;
use App\Services\SlackBot\DataTransferObject\SlackMessageRequestDto;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Notifier\Bridge\Slack\SlackTransport;
use Symfony\Component\Notifier\Chatter;
use Symfony\Component\Notifier\Message\ChatMessage;
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
        try {
            $slackInputLogger->debug('slack message request',[$request->getPathInfo(), $request->getPayload()->all()]);
            $dto = $this->serializer->deserialize(
                $request,
                SlackMessageRequestDto::class,
                'json'
            );
            $slackInputLogger->debug('slack message input dto', [$dto]);

            

        } catch (\Exception $e) {
            $slackInputLogger->error($e->getMessage());

            throw $e;
        }

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

    #[Route('/interactivity', name: '_interactivity', methods: ["POST", "PUT"])]
    public function interactivity(
        Request                 $request,
        SlackInteractivityTransformer $transformer,
        InteractivityPreProcessor     $interactivityPreProcessor,
        CommandProcessor        $commandProcessor,
        LoggerInterface         $slackInputLogger,
        ParameterBagInterface $parameterBag
    ): Response {
        try {
            $payload = $request->getPayload()->get('payload');
            if (null === $payload) {
                throw new \Exception('Cannot get payload');
            }
            $payload = json_decode($payload, true);

            $slackInputLogger->debug('slack interactivity request',[$request->getPathInfo(), $request->getPayload()->all()]);
            $slackInputLogger->debug('slack interactivity payload',[$payload]);

            $slackInputDto = $this->serializer->denormalize($payload, SlackInteractivityInputDto::class);
            $slackInputLogger->debug('slack interactivity input dto', [$slackInputDto]);

            $dto = $transformer->transform($slackInputDto);
            $slackInputLogger->debug('slack interactivity dto', [$dto]);

            $slackCommand = $interactivityPreProcessor->process($dto);
            $slackInputLogger->debug('slack interactivity', [$slackCommand]);

            $answer = $commandProcessor->process($slackCommand);
            $slackInputLogger->debug('slack interactivity answer', [$answer->text]);


        } catch (\Exception $e) {
            $slackInputLogger->error($e->getMessage());

            if ('Cannot get action command' === $e->getMessage()) {
                return new Response('');
            }

            $botApiToken = $parameterBag->get('duty_team_slack_bot_api_token');
            $channelId = $slackCommand->getChannel()->getChannelId();

            $slackTransport = new SlackTransport(
                $botApiToken,
                $channelId
            );
            $chatter = new Chatter($slackTransport);

            $chatMessage = new ChatMessage('`Error`: ' . $e->getMessage());
            $response = $chatter->send($chatMessage);

            throw $e;
        }

        return new Response($answer->text, $answer->code);
    }
}
