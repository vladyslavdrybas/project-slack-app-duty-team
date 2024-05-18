<?php
declare(strict_types=1);

namespace App\Services\DutyTeamSlackBot;

use App\Entity\SlackCommand;
use App\Entity\UserSkills;
use App\Services\DutyTeamSlackBot\Config\CommandList;
use App\Services\DutyTeamSlackBot\DataTransferObject\Command\BotResponseDto;
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
            default => throw new \Exception('Skills command not defined.'),
        };
    }

    public function add(SlackCommand $command): BotResponseDto
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
                    $command->getData()
                )
            )
        );

        if ($hasSkillsAmount < count($skills)) {
            $userSkills->setSkills($skills);

            $this->entityManager->persist($userSkills);
            $this->entityManager->flush();
        }

        $this->slackInputLogger->debug('userSkills', [$skills]);

        $answer = implode(' ', array_map(function ($item) {return '`' . $item . '`';}, $userSkills->getSkills()));
        $answer = 'Skills: ' . $answer;

        return new BotResponseDto($answer);
    }

    public function remove(SlackCommand $command): BotResponseDto
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
                    $command->getData()
                )
            )
        );

        if ($hasSkillsAmount > count($skills)) {
            $userSkills->setSkills($skills);

            $this->entityManager->persist($userSkills);
            $this->entityManager->flush();
        }

        $this->slackInputLogger->debug('userSkills', [$skills]);

        $answer = implode(' ', array_map(function ($item) {return '`' . $item . '`';}, $userSkills->getSkills()));
        $answer = 'Skills: ' . $answer;

        return new BotResponseDto($answer);
    }
}
