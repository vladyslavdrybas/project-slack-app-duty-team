<?php
declare(strict_types=1);

namespace App\Services\DutyTeamSlackBot;

use App\Builder\UserBuilder;
use App\Entity\SlackChannel;
use App\Entity\SlackCommand;
use App\Entity\SlackUser;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Services\DutyTeamSlackBot\Config\CommandName;
use App\Services\DutyTeamSlackBot\DataTransferObject\ISlackMessageIdentifier;
use App\Services\DutyTeamSlackBot\Reader\SlackApiUserReader;
use App\Services\SlackNotifier\Block\SlackActionsBlock;
use App\Services\SlackNotifier\Block\SlackDatePickerBlockElement;
use App\Services\SlackNotifier\Block\SlackInputBlock;
use App\Services\SlackNotifier\Block\SlackTextInputBlockElement;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Notifier\Bridge\Slack\Block\SlackDividerBlock;
use Symfony\Component\Notifier\Bridge\Slack\Block\SlackHeaderBlock;
use Symfony\Component\Notifier\Bridge\Slack\Block\SlackSectionBlock;
use Symfony\Component\Notifier\Bridge\Slack\SlackOptions;
use Symfony\Contracts\HttpClient\HttpClientInterface;

abstract class AbstractPreProcessor
{
    public function __construct(
        protected readonly ParameterBagInterface $parameterBag,
        protected readonly EntityManagerInterface $entityManager,
        protected readonly LoggerInterface $slackInputLogger,
        protected readonly SlackApiUserReader $slackApiUserReader,
        protected readonly UserBuilder $userBuilder,
        readonly protected HttpClientInterface $slackClient
    ) {}

    abstract protected function getCommandName(ISlackMessageIdentifier $dto): CommandName;
    abstract protected function getText(ISlackMessageIdentifier $dto): string;

    public function process(ISlackMessageIdentifier $dto): SlackCommand
    {
        $this->validate($dto);

        $slackUser = $this->getSlackUser($dto);
        if (null === $slackUser) {
            $slackUser = $this->createSlackUser($dto);
        }
        $this->slackInputLogger->debug(__METHOD__,[$slackUser]);

        $slackChannel = $this->getSlackChannel($dto);
        if (null === $slackChannel) {
            $slackChannel = $this->createSlackChannel($dto);
        }
        $this->slackInputLogger->debug(__METHOD__,[$slackChannel]);


        $slackCommand = new SlackCommand();
        $slackCommand->setChannel($slackChannel);
        $slackCommand->setUser($slackUser);

        $slackCommand->setCommandName($this->getCommandName($dto));
        $slackCommand->setText($this->getText($dto));

        if (filter_var($this->parameterBag->get('duty_team_slack_bot_log_command'), FILTER_VALIDATE_BOOLEAN)) {
            $this->entityManager->persist($slackCommand);
            $this->entityManager->flush();
        }

        $this->publishHomeView($slackCommand);

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
        return $this->entityManager->getRepository(SlackUser::class)->findOneBy([
            'userId' => $dto->getUser()->userId,
            'teamId' => $dto->getTeam()->teamId,
        ]);
    }

    protected function createSlackUser(ISlackMessageIdentifier $dto):  ?SlackUser
    {
        $userInfoDto = $this->slackApiUserReader->read($dto);
        $this->slackInputLogger->debug('slack user info dto', [$userInfoDto]);

        if (null === $userInfoDto->email) {
            throw new \Exception("Undefined email address. Check permissions.");
        }

        $slackUser = new SlackUser();

        $slackUser->setUserId($dto->getUser()->userId);
        $slackUser->setUserName($dto->getUser()->userName);
        $slackUser->setTeamId($dto->getTeam()->teamId);
        $slackUser->setTeamDomain($dto->getTeam()->teamDomain);

        $slackUser->setEmail($userInfoDto->email);
        $slackUser->setFullName($userInfoDto->fullName);
        $slackUser->setFirstName($userInfoDto->firstName);
        $slackUser->setLastName($userInfoDto->lastName);

        $slackUser->setTimezone($userInfoDto->timezone);
        $slackUser->setTimezoneLabel($userInfoDto->timezoneLabel);
        $slackUser->setTimezoneOffset($userInfoDto->timezoneOffset);

        $slackUser->setTitle($userInfoDto->title);
        $slackUser->setPhone($userInfoDto->phone);
        $slackUser->setSkype($userInfoDto->skype);
        $slackUser->setAvatar($userInfoDto->avatar);
        $slackUser->setAvatarHash($userInfoDto->avatarHash);
        $slackUser->setColor($userInfoDto->color);

        $slackUser->setIsDeleted($userInfoDto->isDeleted);
        $slackUser->setIsAdmin($userInfoDto->isAdmin);
        $slackUser->setIsOwner($userInfoDto->isOwner);
        $slackUser->setIsPrimaryOwner($userInfoDto->isPrimaryOwner);
        $slackUser->setIsRestricted($userInfoDto->isRestricted);
        $slackUser->setIsUltraRestricted($userInfoDto->isUltraRestricted);
        $slackUser->setIsBot($userInfoDto->isBot);
        $slackUser->setIsAppUser($userInfoDto->isAppUser);
        $slackUser->setIsEmailConfirmed($userInfoDto->isEmailConfirmed);

        /** @var UserRepository $userRepo */
        $userRepo = $this->entityManager->getRepository(User::class);
        $user = $userRepo->findByEmail($userInfoDto->email);

        if (!$user instanceof User) {
            $user = $this->userBuilder->slack($userInfoDto);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }

        $slackUser->setOwner($user);

        $this->entityManager->persist($slackUser);
        $this->entityManager->flush();

        $this->slackInputLogger->debug('slack user', [$slackUser]);

        return $slackUser;
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

    protected function publishHomeView(SlackCommand $command): void
    {
        if ($command->getUser()->isHasView()) {
            return;
        }

        $options = new SlackOptions([
            'type' => 'home'
        ]);

        $options = $this->addSkillsBlock($options);
        $options = $this->addBlocksDivider($options);
        $options = $this->addTimeOffBlock($options);
        $options = $this->addBlocksDivider($options);
        $options = $this->addJiraBlock($options);
        $options = $this->addBlocksDivider($options);

        $response = $this->slackClient->request('POST', '/api/views.publish', [
            'json' => [
                'user_id' => $command->getUser()->getUserId(),
                'view' => $options->toArray(),
            ],
        ]);


        $content = $response->getContent();
        $this->slackInputLogger->debug(__METHOD__, [$response->getInfo()]);
        $this->slackInputLogger->debug(__METHOD__, [$content]);
    }

    protected function addBlocksDivider(SlackOptions $options): SlackOptions
    {
        return $options->block(new SlackDividerBlock());
    }

    protected function addTimeOffBlock(SlackOptions $options): SlackOptions
    {
        return $options
            ->block(new SlackHeaderBlock('Add time off (vacation, holiday, day off, sickday etc.):'))
            ->block(
                (new SlackSectionBlock())
                    ->text('Start date:')
                    ->accessory(
                        new SlackDatePickerBlockElement(
                            new \DateTime('2024-12-30 10:45:55'),
                            'timeoff-start-date',
                            'Start date'
                        )
                    )
            )
            ->block(
                (new SlackSectionBlock())
                    ->text('End date:')
                    ->accessory(
                        new SlackDatePickerBlockElement(
                            new \DateTime('2024-12-30 10:45:55'),
                            'timeoff-end-date',
                            'End date'
                        )
                    )
            )
            ->block(
                (new SlackActionsBlock())
                    ->buttonAction(
                        'Add Time Off',
                        'timeoff-btn-add',
                        'timeoff-btn-add',
                    )
                    ->buttonAction(
                        'Show All Time Off',
                        'timeoff-btn-show',
                        'timeoff-btn-show',
                        'primary'
                    )
            );
    }

    protected function addSkillsBlock(SlackOptions $options): SlackOptions
    {
        return $options
            ->block(new SlackHeaderBlock('Add skills that can be used to solve tasks:'))
            ->block(
                (new SlackInputBlock())
                    ->label('Add skills. Split them via `;`')
                    ->element(
                        new SlackTextInputBlockElement('skills-input-field', true)
                    )
            )
            ->block(
                (new SlackActionsBlock())
                    ->buttonAction(
                        'Add Skills',
                        'skills-btn-add',
                        'skills-btn-add',
                    )
                    ->buttonAction(
                        'Show All Skills',
                        'skills-btn-show',
                        'skills-btn-show'
                    )
            );
    }

    protected function addJiraBlock(SlackOptions $options): SlackOptions
    {
        return $options
            ->block(new SlackHeaderBlock('Integrate with JIRA:'))
            ->block(
                (new SlackActionsBlock())
                    ->buttonAction(
                        'Connect to JIRA',
                        'jira-btn-connect',
                        'jira-btn-connect',
                    )
                    ->buttonAction(
                        'Show JIRA details',
                        'jira-btn-details',
                        'jira-btn-details'
                    )
                    ->buttonAction(
                        'Disconnect JIRA',
                        'jira-btn-disconnect',
                        'jira-btn-disconnect',
                        'danger'
                    )
            );
    }
}
