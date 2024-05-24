<?php
declare(strict_types=1);

namespace App\Services\DutyTeamSlackBot;

use App\Entity\SlackCommand;
use App\Services\DutyTeamSlackBot\DataTransferObject\BotResponseDto;
use App\Services\DutyTeamSlackBot\DataTransferObject\Command\InteractivityDetailsDto;
use App\Services\DutyTeamSlackBot\DataTransferObject\Interactivity\Blocks\ActionCollection;
use App\Services\DutyTeamSlackBot\DataTransferObject\Interactivity\Blocks\StateCollection;
use App\Services\DutyTeamSlackBot\DataTransferObject\Interactivity\ButtonActionElement;
use App\Services\DutyTeamSlackBot\DataTransferObject\Interactivity\DatePickerState;
use App\Services\DutyTeamSlackBot\DataTransferObject\Interactivity\TextInputState;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Notifier\Bridge\Slack\SlackOptions;
use Symfony\Component\Notifier\Bridge\Slack\SlackTransport;
use Symfony\Component\Notifier\Chatter;
use Symfony\Component\Notifier\Message\ChatMessage;
use Symfony\Component\Notifier\Message\SentMessage;

abstract class AbstractCommandProcessor
{
    public function __construct(
        protected readonly ParameterBagInterface $parameterBag,
        protected readonly EntityManagerInterface $entityManager,
        protected readonly LoggerInterface $slackInputLogger
    ) {
    }

    abstract public function process(SlackCommand $command): BotResponseDto;

    protected function mineInteractivityDataFromCommand(string $text): InteractivityDetailsDto
    {
        $data = json_decode($text, true);

        $type = $data['type'] ?? 'undefined';
        $states = new StateCollection();
        $actions = new ActionCollection();

        if (isset($data['states'])) {
            foreach ($data['states'] as $key => $state) {
                switch ($state['type']) {
                    case 'datepicker':
                        $states->offsetSet(
                            $key,
                            new DatePickerState(new DateTime($state['date']['date']))
                        );
                        break;
                    case 'plain_text_input':
                        $states->offsetSet(
                            $key,
                            new TextInputState($state['value'])
                        );
                        break;
                }
            }
        }

        if (isset($data['actions'])) {
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
        }

        return new InteractivityDetailsDto(
            $type,
            $states,
            $actions
        );
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
