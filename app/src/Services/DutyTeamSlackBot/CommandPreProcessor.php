<?php
declare(strict_types=1);

namespace App\Services\DutyTeamSlackBot;

use App\Entity\SlackChannel;
use App\Entity\SlackCommand;
use App\Entity\SlackTeam;
use App\Entity\SlackUser;
use App\Services\DutyTeamSlackBot\Config\CommandList;
use App\Services\DutyTeamSlackBot\DataTransferObject\Command\CommandDto;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class CommandProcessor
{
    public function __construct(
        protected readonly ParameterBagInterface $parameterBag,
        protected readonly EntityManagerInterface $entityManager,
        protected readonly LoggerInterface $slackInputLogger
    ) {
    }

    public function process(CommandDto $commandDto): SlackCommand
    {
        $this->validate($commandDto);

        $slackUser = $this->getSlackUser($commandDto);
        if (null === $slackUser) {
            $slackUser = $this->createSlackUser($commandDto);
        }
        $this->slackInputLogger->debug(__METHOD__,[$slackUser]);

        $slackTeam = $this->getSlackTeam($commandDto);
        if (null === $slackTeam) {
            $slackTeam = $this->createSlackTeam($commandDto);
        }
        $this->slackInputLogger->debug(__METHOD__,[$slackTeam]);

        $slackChannel = $this->getSlackChannel($commandDto);
        if (null === $slackChannel) {
            $slackChannel = $this->createSlackChannel($commandDto);
        }
        $this->slackInputLogger->debug(__METHOD__,[$slackChannel]);

        $slackCommand = new SlackCommand();
        $slackCommand->setTeam($slackTeam);
        $slackCommand->setChannel($slackChannel);
        $slackCommand->setUser($slackUser);
        $slackCommand->setCommandName($commandDto->command);

        $data = match ($commandDto->command) {
            CommandList::SkillsAdd => $this->generateSkillsData($commandDto->text),
            CommandList::SkillsRemove => $this->generateSkillsData($commandDto->text),
        };

        if (empty($data)) {
            throw new \Exception('Invalid data.');
        }

        $slackCommand->setData($data);

        $this->entityManager->persist($slackCommand);
        $this->entityManager->flush();

        return $slackCommand;
    }

    protected function validate(CommandDto $commandDto): void
    {
        if ($commandDto->token !== $this->parameterBag->get('duty_team_slack_bot_verification_token')) {
            throw new \Exception("Invalid bot token.");
        }

        if ($commandDto->apiAppId !== $this->parameterBag->get('duty_team_slack_bot_app_id')) {
            throw new \Exception("Invalid app id.");
        }

        if (null === $commandDto->command) {
            throw new \Exception("Invalid command.");
        }
    }

    protected function getSlackUser(CommandDto $commandDto): ?SlackUser
    {
        return $this->entityManager->getRepository(SlackUser::class)->findOneBy(['userId' => $commandDto->user->userId]);
    }

    protected function createSlackUser(CommandDto $commandDto):  ?SlackUser
    {
        $slackUser = new SlackUser();
        $slackUser->setUserId($commandDto->user->userId);
        $slackUser->setUserName($commandDto->user->userName);

        $this->entityManager->persist($slackUser);
        $this->entityManager->flush();

        return $slackUser;
    }

    protected function getSlackTeam(CommandDto $commandDto): ?SlackTeam
    {
        return $this->entityManager->getRepository(SlackTeam::class)->findOneBy(['teamId' => $commandDto->team->teamId]);
    }

    protected function createSlackTeam(CommandDto $commandDto):  ?SlackTeam
    {
        $slackTeam = new SlackTeam();
        $slackTeam->setTeamId($commandDto->team->teamId);
        $slackTeam->setTeamDomain($commandDto->team->teamDomain);

        $this->entityManager->persist($slackTeam);
        $this->entityManager->flush();

        return $slackTeam;
    }

    protected function getSlackChannel(CommandDto $commandDto): ?SlackChannel
    {
        return $this->entityManager->getRepository(SlackChannel::class)->findOneBy(['channelId' => $commandDto->channel->channelId]);
    }

    protected function createSlackChannel(CommandDto $commandDto):  ?SlackChannel
    {
        $slackChannel = new SlackChannel();
        $slackChannel->setChannelId($commandDto->channel->channelId);
        $slackChannel->setChannelName($commandDto->channel->channelName);

        $this->entityManager->persist($slackChannel);
        $this->entityManager->flush();

        return $slackChannel;
    }

    protected function cleanText(string $text): string
    {
        $text = htmlspecialchars($text);
        $text = htmlentities($text);
        $text = stripslashes($text);

        return $text;
    }

    protected function generateSkillsData(string $text): array
    {
        $text = $this->cleanText($text);

        $data = explode(';', $text);
        $data = array_filter($data, function($item) { return !empty($item); });
        $data = array_map(function($item) { return trim($item); }, $data);
        $data = array_unique($data);

        if (count($data) < 1) {
            return [];
        }

        return $data;
    }
}
