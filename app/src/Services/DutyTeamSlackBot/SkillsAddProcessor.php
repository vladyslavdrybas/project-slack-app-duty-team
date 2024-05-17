<?php
declare(strict_types=1);

namespace App\Services\DutyTeamSlackBot;

use App\Entity\SlackCommand;
use App\Entity\UserSkills;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class SkillsAddProcessor
{
    public function __construct(
        protected readonly ParameterBagInterface $parameterBag,
        protected readonly EntityManagerInterface $entityManager,
        protected readonly LoggerInterface $slackInputLogger
    ) {
    }

    public function add(SlackCommand $command): UserSkills
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

        return $userSkills;
    }

    public function remove(SlackCommand $command): UserSkills
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

        return $userSkills;
    }
}
