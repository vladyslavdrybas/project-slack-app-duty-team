<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'app:test:message:send:slack:http',
    description: 'Test message slack send.',
)]
class TestMessageSendHttpCommand extends Command
{
    public function __construct(
        protected readonly ParameterBagInterface $parameterBag,
        protected readonly HttpClientInterface $slackClient,
        string $name = null
    ) {
        parent::__construct($name);
    }

    protected function configure(): void {}

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->info('Project environment:' . $this->parameterBag->get('kernel.environment'));

        $channelName = 'D073JLYEMQQ';

        $response = $this->slackClient->request('POST', '/api/chat.postMessage', [
            'json' => [
                'channel' => $channelName,
                'text' => 'thank you for your request. id: ' . bin2hex(random_bytes(3)),
            ],
        ]);

        dump($response->getContent());

        $io->success('Success');

        return Command::SUCCESS;
    }
}
