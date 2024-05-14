<?php
declare(strict_types=1);

namespace App\Services\DutyTeamSlackBot;

use App\Entity\SlackChannel;
use App\Entity\SlackTeam;
use App\Entity\SlackUser;
use App\Services\DutyTeamSlackBot\Config\CommandList;
use App\Services\DutyTeamSlackBot\DataTransferObject\Command\CommandDto;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class CommandProcessor
{
    public function __construct(
        protected readonly ParameterBagInterface $parameterBag,
        protected readonly EntityManagerInterface $entityManager
    ) {
    }

    public function process(CommandDto $commandDto): void
    {
        $this->validate($commandDto);

        $slackUser = $this->getSlackUser($commandDto);
        if (null === $slackUser) {
            $slackUser = $this->createSlackUser($commandDto);
        }

        dump($slackUser);

        $slackTeam = $this->getSlackTeam($commandDto);
        if (null === $slackUser) {
            $slackTeam = $this->createSlackTeam($commandDto);
        }

        dump($slackTeam);

        $slackChannel = $this->getSlackChannel($commandDto);
        if (null === $slackUser) {
            $slackChannel = $this->createSlackChannel($commandDto);
        }

        dump($slackChannel);

        match ($commandDto->command) {
            CommandList::SkillsAdd => $this->processAddSkills($commandDto),
        };
    }

    protected function validate(CommandDto $commandDto): void
    {
        if ($commandDto->token !== $this->parameterBag->get('duty_team_slack_bot_verification_token')) {
            throw new \Exception("Invalid bot token.");
        }

        if ($commandDto->apiAppId !== $this->parameterBag->get('duty_team_slack_bot_app_id')) {
            throw new \Exception("Invalid app id.");
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

    protected function processAddSkills(CommandDto $commandDto): void
    {

    }
}
