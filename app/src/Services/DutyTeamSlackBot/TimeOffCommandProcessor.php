<?php
declare(strict_types=1);

namespace App\Services\DutyTeamSlackBot;

use App\Entity\SlackCommand;
use App\Entity\UserTimeOff;
use App\Services\DutyTeamSlackBot\Config\CommandList;
use App\Services\DutyTeamSlackBot\DataTransferObject\BotResponseDto;
use App\Services\DutyTeamSlackBot\DataTransferObject\Interactivity\Blocks\ActionCollection;
use App\Services\DutyTeamSlackBot\DataTransferObject\Interactivity\Blocks\StateCollection;
use App\Services\DutyTeamSlackBot\DataTransferObject\Interactivity\ButtonActionElement;
use App\Services\DutyTeamSlackBot\DataTransferObject\Interactivity\DatePickerState;
use App\Services\SlackNotifier\Block\SlackActionsBlock;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\ArrayShape;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Notifier\Bridge\Slack\Block\SlackDividerBlock;
use Symfony\Component\Notifier\Bridge\Slack\SlackOptions;
use Symfony\Component\Notifier\Bridge\Slack\SlackTransport;
use Symfony\Component\Notifier\Chatter;
use Symfony\Component\Notifier\Message\ChatMessage;

class TimeOffCommandProcessor
{
    public function __construct(
        protected readonly ParameterBagInterface $parameterBag,
        protected readonly EntityManagerInterface $entityManager,
        protected readonly LoggerInterface $slackInputLogger
    ) {
    }

    public function process(SlackCommand $command): BotResponseDto
    {
        return match ($command->getCommandName()) {
            CommandList::TimeOff => $this->sendInteractivityForm($command),
            CommandList::TimeOffBtnAdd => $this->add($command),
            default => throw new \Exception('Time off command not defined.'),
        };
    }

    protected function sendInteractivityForm(SlackCommand $command): BotResponseDto
    {
        $botApiToken = $this->parameterBag->get('duty_team_slack_bot_api_token');
        $channelId = $command->getChannel()->getChannelId();
        $text = 'Add Time Off:';

        $slackTransport = new SlackTransport(
            $botApiToken,
            $channelId
        );
        $chatter = new Chatter($slackTransport);

        $chatMessage = new ChatMessage($text);

        $slackOptions = (new SlackOptions())
            ->block(
                (new SlackActionsBlock())
                ->datepicker(
                    new \DateTime('2024-12-30 10:45:55'),
                    'timeoff-start-date',
                    'Start date'
                )
                ->datepicker(
                    new \DateTime('2024-12-30 10:45:55'),
                    'timeoff-end-date',
                    'End date'
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
            );

        // Add the custom options to the chat message and send the message
        $chatMessage->options($slackOptions);

        $response = $chatter->send($chatMessage);

        $this->slackInputLogger->debug('slack response on timeoff add', [$response]);

        return new BotResponseDto('');
    }

    #[ArrayShape([
        'type' => 'string',
        'state' => StateCollection::class,
        'actions' => ActionCollection::class,
    ])]
    protected function mineDataFromAddRemoveCommand(string $text): array
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

        $data['state'] = $states;
        $data['actions'] = $actions;

        return $data;
    }

    protected function add(SlackCommand $command): BotResponseDto
    {
        $data = $this->mineDataFromAddRemoveCommand($command->getText());
        $this->slackInputLogger->debug('slack command timeoff-btn-add data', [$data]);

        $startDate = $data['state']->offsetGet('timeoff-start-date')->date;
        $endDate = $data['state']->offsetGet('timeoff-end-date')->date;

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

        $this->sendCommandAnswer($command, $answer);

        return $answer;
    }

    protected function sendCommandAnswer(SlackCommand $command, BotResponseDto $answer): void
    {
        $botApiToken = $this->parameterBag->get('duty_team_slack_bot_api_token');
        $channelId = $command->getChannel()->getChannelId();

        $slackTransport = new SlackTransport(
            $botApiToken,
            $channelId
        );
        $chatter = new Chatter($slackTransport);

        $chatMessage = new ChatMessage($answer->text);
        $response = $chatter->send($chatMessage);
    }
}
