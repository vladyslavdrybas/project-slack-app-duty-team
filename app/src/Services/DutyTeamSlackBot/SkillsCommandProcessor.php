<?php
declare(strict_types=1);

namespace App\Services\DutyTeamSlackBot;

use App\Entity\SlackCommand;
use App\Entity\UserSkills;
use App\Services\DutyTeamSlackBot\Config\CommandList;
use App\Services\DutyTeamSlackBot\DataTransferObject\BotResponseDto;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class SkillsCommandProcessor
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
            CommandList::SkillsAdd => $this->add($command),
            CommandList::SkillsRemove => $this->remove($command),
            CommandList::SkillsShow => $this->show($command),
            default => throw new \Exception('Skills command not defined.'),
        };
    }

    protected function answerAllSkills(array $skills): BotResponseDto
    {
        $answer = implode(' ', array_map(function ($item) {return '`' . $item . '`';}, $skills));
        $answer = 'Skills: ' . $answer;

        return new BotResponseDto($answer);
    }

    protected function mineDataFromCommand(string $text): array
    {
        $data = explode(';', $text);
        $data = array_filter($data, function($item) { return !empty($item); });
        $data = array_map(function($item) { return trim($item); }, $data);
        $data = array_unique($data);

        if (count($data) < 1) {
            return [];
        }

        return $data;
    }

    protected function add(SlackCommand $command): BotResponseDto
    {
        $userSkills = $this->entityManager->getRepository(UserSkills::class)->findOneBy([
            'slackUser' => $command->getUser(),
        ]);

        if (null === $userSkills) {
            $userSkills = new UserSkills();
            $userSkills->setSlackUser($command->getUser());
        }
        $hasSkillsAmount = count($userSkills->getSkills());

        $skills = array_values(
            array_unique(
                array_merge(
                    $userSkills->getSkills(),
                    $this->mineDataFromCommand($command->getText())
                )
            )
        );

        if ($hasSkillsAmount < count($skills)) {
            $userSkills->setSkills($skills);

            $this->entityManager->persist($userSkills);
            $this->entityManager->flush();
        }

        $this->slackInputLogger->debug('userSkills', [$skills]);

        return $this->answerAllSkills($userSkills->getSkills());
    }

    protected function remove(SlackCommand $command): BotResponseDto
    {
        $userSkills = $this->entityManager->getRepository(UserSkills::class)->findOneBy([
            'slackUser' => $command->getUser(),
        ]);

        if (null === $userSkills) {
            $userSkills = new UserSkills();
            $userSkills->setSlackUser($command->getUser());
        }
        $hasSkillsAmount = count($userSkills->getSkills());

        $skills = array_values(
            array_unique(
                array_diff(
                    $userSkills->getSkills(),
                    $this->mineDataFromCommand($command->getText())
                )
            )
        );

        if ($hasSkillsAmount > count($skills)) {
            $userSkills->setSkills($skills);

            $this->entityManager->persist($userSkills);
            $this->entityManager->flush();
        }

        $this->slackInputLogger->debug('userSkills', [$skills]);

        return $this->answerAllSkills($userSkills->getSkills());
    }

    protected function show(SlackCommand $command): BotResponseDto
    {
        $userSkills = $this->entityManager->getRepository(UserSkills::class)->findOneBy([
            'slackUser' => $command->getUser(),
        ]);

        $skills = [];
        if (null !== $userSkills) {
            $skills = $userSkills->getSkills();
        }

        return $this->answerAllSkills($skills);
    }
}
