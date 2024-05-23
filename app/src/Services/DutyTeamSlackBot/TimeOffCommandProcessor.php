<?php
declare(strict_types=1);

namespace App\Services\DutyTeamSlackBot;

use App\Entity\SlackCommand;
use App\Entity\UserTimeOff;
use App\Services\DutyTeamSlackBot\Config\CommandList;
use App\Services\DutyTeamSlackBot\DataTransferObject\BotResponseDto;
use App\Services\DutyTeamSlackBot\DataTransferObject\Command\InteractivityDetailsDto;
use App\Services\DutyTeamSlackBot\DataTransferObject\Interactivity\Blocks\ActionCollection;
use App\Services\DutyTeamSlackBot\DataTransferObject\Interactivity\Blocks\StateCollection;
use App\Services\DutyTeamSlackBot\DataTransferObject\Interactivity\ButtonActionElement;
use App\Services\DutyTeamSlackBot\DataTransferObject\Interactivity\DatePickerState;
use App\Services\SlackNotifier\Block\SlackActionsBlock;
use App\Services\SlackNotifier\Block\SlackDatePickerBlockElement;
use App\Services\SlackNotifier\Block\SlackTextWithActionButtonBlockElement;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Notifier\Bridge\Slack\Block\SlackDividerBlock;
use Symfony\Component\Notifier\Bridge\Slack\Block\SlackHeaderBlock;
use Symfony\Component\Notifier\Bridge\Slack\Block\SlackSectionBlock;
use Symfony\Component\Notifier\Bridge\Slack\SlackOptions;
use Symfony\Component\Notifier\Bridge\Slack\SlackTransport;
use Symfony\Component\Notifier\Chatter;
use Symfony\Component\Notifier\Message\ChatMessage;
use Symfony\Component\Notifier\Message\SentMessage;

class TimeOffCommandProcessor
{
    public function __construct(
        protected readonly ParameterBagInterface  $parameterBag,
        protected readonly EntityManagerInterface $entityManager,
        protected readonly LoggerInterface        $slackInputLogger
    )
    {
    }

    public function process(SlackCommand $command): BotResponseDto
    {
        return match ($command->getCommandName()) {
            CommandList::TimeOff => $this->sendInteractivityForm($command),
            CommandList::TimeOffBtnAdd => $this->add($command),
            CommandList::TimeOffBtnShow => $this->show($command),
            CommandList::TimeOffBtnRemove => $this->remove($command),
            default => throw new \Exception('Time off command not defined.'),
        };
    }

    protected function sendInteractivityForm(SlackCommand $command): BotResponseDto
    {
        $slackOptions = (new SlackOptions())
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
            ->block(new SlackDividerBlock())
            ->block(
                (new SlackActionsBlock())
                    ->buttonAction(
                        'Add Time Off',
                        'timeoff-btn-add',
                        'timeoff-btn-add',
                    )
                    ->buttonAction(
                        'Remove Time Off',
                        'timeoff-btn-remove',
                        'timeoff-btn-remove',
                        'danger'
                    )
                    ->buttonAction(
                        'Show All Time Off',
                        'timeoff-btn-show',
                        'timeoff-btn-show',
                        'primary'
                    )
            );

        $response = $this->sendCommandAnswer(
            $command,
            'Time Off',
            $slackOptions
        );

        $this->slackInputLogger->debug('slack response on timeoff add', [$response]);

        return new BotResponseDto('');
    }

    protected function mineDataFromAddRemoveCommand(string $text): InteractivityDetailsDto
    {
        $data = json_decode($text, true);

        $states = new StateCollection();
        $actions = new ActionCollection();

        foreach ($data['state'] as $key => $state) {
            if ('datepicker' === $state['type']) {
                $states->offsetSet(
                    $key,
                    new DatePickerState(new DateTime($state['date']['date']))
                );
            }
        }

        foreach ($data['actions'] as $action) {
            if ('button' === $action['type']) {
                $actions->append(
                    new ButtonActionElement(
                        $action['actionId'],
                        $action['value'],
                        $action['text']
                    )
                );
            }
        }

        return new InteractivityDetailsDto(
            $data['type'],
            $states,
            $actions
        );
    }

    protected function add(SlackCommand $command): BotResponseDto
    {
        $data = $this->mineDataFromAddRemoveCommand($command->getText());
        $this->slackInputLogger->debug('slack command timeoff-btn-add data', [$data]);

        $startDate = $data->states->offsetGet('timeoff-start-date')->date;
        $endDate = $data->states->offsetGet('timeoff-end-date')->date;

        if ($startDate > $endDate) {
            throw new \Exception('Wrong time range');
        }

        $timeOffExist = $this->entityManager->getRepository(UserTimeOff::class)->findOneBy([
            'startAt' => $startDate,
            'endAt' => $endDate,
        ]);

        if (!$timeOffExist instanceof UserTimeOff) {
            $timeOff = new UserTimeOff();
            $timeOff->setUser($command->getUser());
            $timeOff->setStartAt($startDate);
            $timeOff->setEndAt($endDate);

            $this->slackInputLogger->debug('set new user time off', [$timeOff]);
            $this->entityManager->persist($timeOff);
            $this->entityManager->flush();
        }

        $startDateFormatted = $startDate->format('Y-m-d');
        $endDateFormatted = $endDate->format('Y-m-d');

        if ($startDateFormatted === $endDateFormatted) {
            $text = 'You added time off for `'
                . $startDateFormatted
                . '`';
        } else {
            $text = 'You added time off from `'
                . $startDateFormatted
                . '` to `'
                . $endDateFormatted
                . '`';
        }

        $answer = new BotResponseDto($text);

        $this->sendCommandAnswer($command, $answer->text);

        return $answer;
    }

    protected function remove(SlackCommand $command): BotResponseDto
    {
        $data = $this->mineDataFromAddRemoveCommand($command->getText());
        $this->slackInputLogger->debug(
            sprintf('slack command %s data', CommandList::TimeOffBtnRemove->value),
            [$data]
        );

        $text = 'Unknown time off to remove.';

        /** @var ButtonActionElement $action */
        foreach ($data->actions as $action) {
            if (CommandList::TimeOffBtnRemove->value === $action->getActionId()) {
                $timeOff = $this->entityManager->getRepository(UserTimeOff::class)->find($action->getValue());
                if ($timeOff instanceof UserTimeOff) {
                    $this->entityManager->remove($timeOff);
                    $this->entityManager->flush();

                    $text = 'Removed: ' . $this->convertTimeOffToString($timeOff);
                }
            }
        };

        $answer = new BotResponseDto($text);

        $showCommand = new SlackCommand();
        $showCommand->setCommandName(CommandList::TimeOffBtnShow);
        $showCommand->setText(
            sprintf(
                '{"parentCommand":"%s","parentId":"%s"',
                $command->getCommandName()->value,
                $command->getRawId()
            )
        );
        $showCommand->setTeam($command->getTeam());
        $showCommand->setUser($command->getUser());
        $showCommand->setChannel($command->getChannel());

        $this->show($showCommand);

        return $answer;
    }

    protected function show(SlackCommand $command): BotResponseDto
    {
        $timeOffCollection = $this->entityManager->getRepository(UserTimeOff::class)
            ->findBy([
                'user' => $command->getUser(),
            ]);

        $answer = new BotResponseDto('');

        $slackOptions = (new SlackOptions())
            ->block(new SlackDividerBlock())
            ->block(new SlackHeaderBlock('Time Off that you have:'));

        foreach ($timeOffCollection as $timeOff) {
            $text = $this->convertTimeOffToString($timeOff);

            $slackOptions->block(
                (new SlackSectionBlock())
                    ->text($text)
                    ->accessory(
                        new SlackTextWithActionButtonBlockElement(
                            'remove',
                            'timeoff-btn-remove',
                            $timeOff->getRawId(),
                            'danger'
                        )
                    )
            );
        }

        if (0 === count($timeOffCollection)) {
            $slackOptions->block((new SlackSectionBlock())->text('no dates yet'));
        }

        $slackOptions->block(new SlackDividerBlock());

        $this->sendCommandAnswer(
            $command,
            $answer->text,
            $slackOptions
        );

        return $answer;
    }

    protected function convertTimeOffToString(UserTimeOff $timeOff): string
    {
        $startAt = $timeOff->getStartAt()->format('Y-m-d');
        $endAt = $timeOff->getEndAt()->format('Y-m-d');
        $text = '`' . $startAt . '`';
        if ($startAt !== $endAt) {
            $text .= ' - ';
            $text .= '`' . $endAt . '`';
        }

        return $text;
    }

    protected function sendCommandAnswer(
        SlackCommand $command,
        string $text,
        ?SlackOptions $options = null
    ): ?SentMessage {
        $botApiToken = $this->parameterBag->get('duty_team_slack_bot_api_token');
        $channelId = $command->getChannel()->getChannelId();

        $slackTransport = new SlackTransport(
            $botApiToken,
            $channelId
        );
        $chatter = new Chatter($slackTransport);

        $chatMessage = new ChatMessage($text);

        if (null !== $options) {
            $chatMessage->options($options);
        }

        return $chatter->send($chatMessage);
    }
}
