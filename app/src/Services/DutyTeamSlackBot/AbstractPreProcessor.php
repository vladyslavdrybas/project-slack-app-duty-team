<?php
declare(strict_types=1);

namespace App\Services\DutyTeamSlackBot;

use App\Entity\SlackChannel;
use App\Entity\SlackCommand;
use App\Entity\SlackTeam;
use App\Entity\SlackUser;
use App\Services\DutyTeamSlackBot\Config\CommandList;
use App\Services\DutyTeamSlackBot\DataTransferObject\ISlackMessageIdentifier;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

abstract class AbstractPreProcessor
{
    public function __construct(
        protected readonly ParameterBagInterface $parameterBag,
        protected readonly EntityManagerInterface $entityManager,
        protected readonly LoggerInterface $slackInputLogger,
    ) {
    }

    abstract protected function getCommandName(ISlackMessageIdentifier $dto): CommandList;
    abstract protected function getText(ISlackMessageIdentifier $dto): string;

    public function process(ISlackMessageIdentifier $dto): SlackCommand
    {
        $this->validate($dto);

        $slackUser = $this->getSlackUser($dto);
        if (null === $slackUser) {
            $slackUser = $this->createSlackUser($dto);
        }
        $this->slackInputLogger->debug(__METHOD__,[$slackUser]);

        $slackTeam = $this->getSlackTeam($dto);
        if (null === $slackTeam) {
            $slackTeam = $this->createSlackTeam($dto);
        }
        $this->slackInputLogger->debug(__METHOD__,[$slackTeam]);

        $slackChannel = $this->getSlackChannel($dto);
        if (null === $slackChannel) {
            $slackChannel = $this->createSlackChannel($dto);
        }
        $this->slackInputLogger->debug(__METHOD__,[$slackChannel]);

        $slackCommand = new SlackCommand();
        $slackCommand->setTeam($slackTeam);
        $slackCommand->setChannel($slackChannel);
        $slackCommand->setUser($slackUser);

        $slackCommand->setCommandName($this->getCommandName($dto));
        $slackCommand->setText($this->getText($dto));

        if (filter_var($this->parameterBag->get('duty_team_slack_bot_log_command'), FILTER_VALIDATE_BOOLEAN)
            && !empty($slackCommand->getText())
        ) {
            $this->entityManager->persist($slackCommand);
            $this->entityManager->flush();
        }

        return $slackCommand;
    }

    protected function validate(ISlackMessageIdentifier $dto): void
    {
        if ($dto->getToken() !== $this->parameterBag->get('duty_team_slack_bot_verification_token')) {
            throw new \Exception("Invalid bot token.");
        }

        if ($dto->getApiAppId() !== $this->parameterBag->get('duty_team_slack_bot_app_id')) {
            throw new \Exception("Invalid app id.");
        }
    }

    protected function getSlackUser(ISlackMessageIdentifier $dto): ?SlackUser
    {
        return $this->entityManager->getRepository(SlackUser::class)->findOneBy(['userId' => $dto->getUser()->userId]);
    }

    protected function createSlackUser(ISlackMessageIdentifier $dto):  ?SlackUser
    {
        $slackUser = new SlackUser();
        $slackUser->setUserId($dto->getUser()->userId);
        $slackUser->setUserName($dto->getUser()->userName);

        $this->entityManager->persist($slackUser);
        $this->entityManager->flush();

        return $slackUser;
    }

    protected function getSlackTeam(ISlackMessageIdentifier $dto): ?SlackTeam
    {
        return $this->entityManager->getRepository(SlackTeam::class)->findOneBy(['teamId' => $dto->getTeam()->teamId]);
    }

    protected function createSlackTeam(ISlackMessageIdentifier $dto):  ?SlackTeam
    {
        $slackTeam = new SlackTeam();
        $slackTeam->setTeamId($dto->getTeam()->teamId);
        $slackTeam->setTeamDomain($dto->getTeam()->teamDomain);

        $this->entityManager->persist($slackTeam);
        $this->entityManager->flush();

        return $slackTeam;
    }

    protected function getSlackChannel(ISlackMessageIdentifier $dto): ?SlackChannel
    {
        return $this->entityManager->getRepository(SlackChannel::class)->findOneBy(['channelId' => $dto->getChannel()->channelId]);
    }

    protected function createSlackChannel(ISlackMessageIdentifier $dto):  ?SlackChannel
    {
        $slackChannel = new SlackChannel();
        $slackChannel->setChannelId($dto->getChannel()->channelId);
        $slackChannel->setChannelName($dto->getChannel()->channelName);

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
}
