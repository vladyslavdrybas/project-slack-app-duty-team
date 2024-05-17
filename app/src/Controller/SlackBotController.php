<?php

namespace App\Controller;

use App\Services\DutyTeamSlackBot\CommandProcessor;
use App\Services\DutyTeamSlackBot\DataTransferObject\Command\SlackCommandInputDto;
use App\Services\DutyTeamSlackBot\DataTransferObject\Transformer\SlackCommandTransformer;
use App\Services\DutyTeamSlackBot\SkillsAddProcessor;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
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

    #[Route('/skills/add', name: '_command_skills_add', methods: ["POST", "PUT"])]
    public function skillsAdd(
        Request $request,
//        #[MapRequestPayload] SlackCommandInputDto $slackCommandInputDto,
        SlackCommandTransformer $transformer,
        CommandProcessor $commandProcessor,
        SkillsAddProcessor $skillsAddProcessor,
        LoggerInterface $slackInputLogger
    ): Response {
        try {
            $payload =  $request->getPayload()->all();
            $slackInputLogger->debug('slack request',[$request->getPathInfo(), $request->getPayload()->all()]);

            $slackCommandInputDto = $this->serializer->denormalize($payload, SlackCommandInputDto::class);
            $slackInputLogger->debug('slack command input dto', [$slackCommandInputDto]);

            $dto = $transformer->transform($slackCommandInputDto);
            $slackInputLogger->debug('slack command dto', [$dto]);

            $slackCommand = $commandProcessor->process($dto);
            $slackInputLogger->debug('slack command', [$slackCommand]);

            $skills = $skillsAddProcessor->add($slackCommand);
            $slackInputLogger->debug('userSkills', [$skills]);
        } catch (\Exception $e) {
            $slackInputLogger->error($e->getMessage());

            throw $e;
        }

        $answer = implode(' ', array_map(function ($item) {return '`' . $item . '`';}, $slackCommand->getData()));

        return new Response('Added skills: ' . $answer, Response::HTTP_OK);
    }

    #[Route('/skills/remove', name: '_command_skills_remove', methods: ["POST", "PUT"])]
    public function skillsRemove(
        Request $request,
//        #[MapRequestPayload] SlackCommandInputDto $slackCommandInputDto,
        SlackCommandTransformer $transformer,
        CommandProcessor $commandProcessor,
        SkillsAddProcessor $skillsAddProcessor,
        LoggerInterface $slackInputLogger
    ): Response {
        try {
            $payload =  $request->getPayload()->all();
            $slackInputLogger->debug('slack request',[$request->getPathInfo(), $request->getPayload()->all()]);

            $slackCommandInputDto = $this->serializer->denormalize($payload, SlackCommandInputDto::class);
            $slackInputLogger->debug('slack command input dto', [$slackCommandInputDto]);

            $dto = $transformer->transform($slackCommandInputDto);
            $slackInputLogger->debug('slack command dto', [$dto]);

            $slackCommand = $commandProcessor->process($dto);
            $slackInputLogger->debug('slack command', [$slackCommand]);

            $skills = $skillsAddProcessor->remove($slackCommand);
            $slackInputLogger->debug('userSkills', [$skills]);
        } catch (\Exception $e) {
            $slackInputLogger->error($e->getMessage());

            throw $e;
        }

        $answer = implode(' ', array_map(function ($item) {return '`' . $item . '`';}, $slackCommand->getData()));

        return new Response('Removed skills: ' . $answer, Response::HTTP_OK);
    }
}
