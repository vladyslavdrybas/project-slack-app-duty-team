<?php
declare(strict_types=1);

namespace App\Services\DutyTeamSlackBot;

use App\Entity\SlackCommand;
use App\Entity\UserSkills;
use App\Services\DutyTeamSlackBot\Config\CommandList;
use App\Services\DutyTeamSlackBot\DataTransferObject\BotResponseDto;
use App\Services\SlackNotifier\Block\SlackActionsBlock;
use App\Services\SlackNotifier\Block\SlackInputBlock;
use App\Services\SlackNotifier\Block\SlackTextInputBlockElement;
use Symfony\Component\Notifier\Bridge\Slack\Block\SlackDividerBlock;
use Symfony\Component\Notifier\Bridge\Slack\Block\SlackHeaderBlock;
use Symfony\Component\Notifier\Bridge\Slack\SlackOptions;

class SkillsCommandProcessor extends AbstractCommandProcessor
{
    public function process(SlackCommand $command): BotResponseDto
    {
        return match ($command->getCommandName()) {
            CommandList::Skills => $this->interactivityForm($command),
            CommandList::SkillsBtnAdd => $this->add($command),
            CommandList::SkillsBtnShow => $this->show($command),
            CommandList::SkillsBtnRemove => $this->remove($command),
            default => throw new \Exception('Skills command not defined.'),
        };
    }

    protected function interactivityForm(SlackCommand $command): BotResponseDto
    {
        $options = new SlackOptions();

        $slackOptions = $options
            ->block(new SlackHeaderBlock('Add skills that can be used to solve tasks:'))
            ->block(
                (new SlackInputBlock())
                    ->label('Add skills. Split them via `;`')
                    ->element(
                        new SlackTextInputBlockElement('skills-input-field')
                    )
            )
            ->block(new SlackDividerBlock())
            ->block(
                (new SlackActionsBlock())
                    ->buttonAction(
                        'Add Skills',
                        'skills-btn-add',
                        'skills-btn-add',
                    )
                    ->buttonAction(
                        'Remove Skills',
                        'skills-btn-remove',
                        'skills-btn-remove',
                        'danger'
                    )
                    ->buttonAction(
                        'Show All Skills',
                        'skills-btn-show',
                        'skills-btn-show'
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

    protected function answerAllSkills(array $skills): BotResponseDto
    {
        $answer = implode(' ', array_map(function ($item) {return '`' . $item . '`';}, $skills));
        $answer = 'Skills: ' . $answer;

        return new BotResponseDto($answer);
    }

    protected function mineSkills(string $text): array
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
        $data = $this->mineInteractivityDataFromCommand($command->getText());
        $this->slackInputLogger->debug('slack command timeoff-btn-add data', [$data]);

        $text = $data->states->offsetGet('skills-input-field')->value;

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
                    $this->mineSkills($text)
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
                    $this->mineSkills($command->getText())
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
