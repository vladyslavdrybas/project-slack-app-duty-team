<?php
declare(strict_types=1);

namespace App\Services\DutyTeamSlackBot;

use App\Entity\SlackCommand;
use App\Entity\UserSkills;
use App\Services\DutyTeamSlackBot\Config\CommandName;
use App\Services\DutyTeamSlackBot\DataTransferObject\BotResponseDto;
use App\Services\SlackNotifier\Block\SlackActionsBlock;
use App\Services\SlackNotifier\Block\SlackInputBlock;
use App\Services\SlackNotifier\Block\SlackTextInputBlockElement;
use Symfony\Component\Notifier\Bridge\Slack\Block\SlackDividerBlock;
use Symfony\Component\Notifier\Bridge\Slack\Block\SlackHeaderBlock;
use Symfony\Component\Notifier\Bridge\Slack\Block\SlackSectionBlock;
use Symfony\Component\Notifier\Bridge\Slack\SlackOptions;

class SkillsCommandProcessor extends AbstractCommandProcessor
{
    public function process(SlackCommand $command): BotResponseDto
    {
        return match ($command->getCommandName()) {
            CommandName::Skills => $this->interactivityForm($command),
            CommandName::SkillsBtnAdd => $this->add($command),
            CommandName::SkillsBtnShow => $this->show($command),
            CommandName::SkillsBtnRemove => $this->remove($command),
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

        $userSkills = $this->getSkillsByCommand($command);

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

        $this->show($command);

        return $this->answerAllSkills($userSkills->getSkills());
    }

    protected function remove(SlackCommand $command): BotResponseDto
    {
        $data = $this->mineInteractivityDataFromCommand($command->getText());
        $this->slackInputLogger->debug('slack command timeoff-btn-add data', [$data]);

        $text = $data->states->offsetGet('skills-input-field')->value;

        $userSkills = $this->getSkillsByCommand($command);

        $hasSkillsAmount = count($userSkills->getSkills());

        $skills = array_values(
            array_unique(
                array_diff(
                    $userSkills->getSkills(),
                    $this->mineSkills($text)
                )
            )
        );

        if ($hasSkillsAmount > count($skills)) {
            $userSkills->setSkills($skills);

            $this->entityManager->persist($userSkills);
            $this->entityManager->flush();
        }

        $this->slackInputLogger->debug('userSkills', [$skills]);

        $this->show($command);

        return $this->answerAllSkills($userSkills->getSkills());
    }

    protected function show(SlackCommand $command): BotResponseDto
    {
        $userSkills = $this->getSkillsByCommand($command);

        $answer = new BotResponseDto('');

        $options = new SlackOptions();

        $slackOptions = $options
            ->block(new SlackHeaderBlock('Skills that you have:'));

        $text = '';
        foreach ($userSkills->getSkills() as $skill) {
            $text .= ' `' . $skill . '` ';
        }

        if (0 === count($userSkills->getSkills())) {
            $text = 'No skills yet.';
        }

        $slackOptions->block(
            (new SlackSectionBlock())
                ->text($text)
        );

        $slackOptions->block(new SlackDividerBlock());

        $this->sendCommandAnswer(
            $command,
            $answer->text,
            $slackOptions
        );

        return $answer;
    }

    protected function getSkillsByCommand(SlackCommand $command): UserSkills
    {
        $userSkills = $this->entityManager->getRepository(UserSkills::class)->findOneBy([
            'owner' => $command->getUser()->getOwner(),
        ]);

        if (null === $userSkills) {
            $userSkills = new UserSkills();
            $userSkills->setOwner($command->getUser()->getOwner());
        }

        return $userSkills;
    }
}
