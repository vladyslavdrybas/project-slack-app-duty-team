<?php
declare(strict_types=1);

namespace App\Services\DutyTeamSlackBot;

use App\Entity\SlackCommand;
use App\Services\DutyTeamSlackBot\Config\CommandList;
use App\Services\DutyTeamSlackBot\DataTransferObject\Command\BotResponseDto;
use App\Services\SlackNotifier\Block\SlackActionsBlock;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Notifier\Bridge\Slack\Block\SlackDividerBlock;
use Symfony\Component\Notifier\Bridge\Slack\SlackOptions;
use Symfony\Component\Notifier\Bridge\Slack\SlackTransport;
use Symfony\Component\Notifier\Chatter;
use Symfony\Component\Notifier\Message\ChatMessage;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class TimeOffCommandProcessor
{
    public function __construct(
        protected readonly ParameterBagInterface $parameterBag,
        protected readonly EntityManagerInterface $entityManager,
        protected readonly LoggerInterface $slackInputLogger,
        protected readonly HttpClientInterface $httpClient,
    ) {
    }

    public function process(SlackCommand $command): BotResponseDto
    {
        return match ($command->getCommandName()) {
            CommandList::TimeOff => $this->sendInteractivityForm($command),
            default => throw new \Exception('Time off command not defined.'),
        };
    }

    protected function answerAllSkills(array $data): BotResponseDto
    {
        $answer = implode(' ', array_map(function ($item) {return '`' . $item . '`';}, $data));
        $answer = 'Time off: ' . $answer;

        return new BotResponseDto($answer);
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
}
