<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Notifier\Bridge\Slack\SlackTransport;
use Symfony\Component\Notifier\Chatter;
use Symfony\Component\Notifier\Message\ChatMessage;

#[AsCommand(
    name: 'app:test:message:send:slack:chat',
    description: 'Test message slack send.',
)]
class TestMessageSendNotifierCommand extends Command
{
    public function __construct(
        protected readonly ParameterBagInterface $parameterBag,
        string $name = null
    ) {
        parent::__construct($name);
    }

    protected function configure(): void {}

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->info('Project environment:' . $this->parameterBag->get('kernel.environment'));

        $botApiToken = $this->parameterBag->get('slack_bot_api_token');
        $channelName = 'D073JLYEMQQ';
        $text = 'thank you for your request. chatter. id: ' . bin2hex(random_bytes(3));

        $slackTransport = new SlackTransport($botApiToken, $channelName);
        $chatter = new Chatter($slackTransport);
        $chatMessage = new ChatMessage($text);
        $response = $chatter->send($chatMessage);

        dump($response);

        $io->success('Success');

        return Command::SUCCESS;
    }
}
