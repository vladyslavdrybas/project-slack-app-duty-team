<?php
declare(strict_types=1);

namespace App\Services\DutyTeamSlackBot;

use App\Entity\SlackCommand;
use App\Entity\UserTimeOff;
use App\Services\DutyTeamSlackBot\Config\CommandName;
use App\Services\DutyTeamSlackBot\DataTransferObject\BotResponseDto;
use App\Services\DutyTeamSlackBot\DataTransferObject\Interactivity\ButtonActionElement;
use App\Services\SlackNotifier\Block\SlackActionsBlock;
use App\Services\SlackNotifier\Block\SlackDatePickerBlockElement;
use App\Services\SlackNotifier\Block\SlackTextWithActionButtonBlockElement;
use Symfony\Component\Notifier\Bridge\Slack\Block\SlackDividerBlock;
use Symfony\Component\Notifier\Bridge\Slack\Block\SlackHeaderBlock;
use Symfony\Component\Notifier\Bridge\Slack\Block\SlackSectionBlock;
use Symfony\Component\Notifier\Bridge\Slack\SlackOptions;

// TODO add user timezone recognition on timeoff
class TimeOffCommandProcessor extends AbstractCommandProcessor
{
    public function process(SlackCommand $command): BotResponseDto
    {
        return match ($command->getCommandName()) {
            CommandName::TimeOff => $this->interactivityForm($command),
            CommandName::TimeOffBtnAdd => $this->add($command),
            CommandName::TimeOffBtnShow => $this->show($command),
            CommandName::TimeOffBtnRemove => $this->remove($command),
            default => throw new \Exception('Time off command not defined.'),
        };
    }

    protected function convertTimeOffToString(UserTimeOff $timeOff): string
    {
        $startAt = $timeOff->getStartAt()->format('Y-m-d');
        $endAt = $timeOff->getEndAt()->format('Y-m-d');
        $text = '`' . $startAt . '`';
        if ($startAt !== $endAt) {
            $text .= ' - ';
            $text .= '`' . $endAt . '`';
        }

        return $text;
    }

    protected function interactivityForm(SlackCommand $command): BotResponseDto
    {
        $options = new SlackOptions();

        $slackOptions = $options
            ->block(
                (new SlackSectionBlock())
                    ->text('Start date:')
                    ->accessory(
                        new SlackDatePickerBlockElement(
                            new \DateTime('2024-12-30 10:45:55'),
                            'timeoff-start-date',
                            'Start date'
                        )
                    )
            )
            ->block(
                (new SlackSectionBlock())
                    ->text('End date:')
                    ->accessory(
                        new SlackDatePickerBlockElement(
                            new \DateTime('2024-12-30 10:45:55'),
                            'timeoff-end-date',
                            'End date'
                        )
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
                    ->buttonAction(
                        'Show All Time Off',
                        'timeoff-btn-show',
                        'timeoff-btn-show',
                        'primary'
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

    protected function add(SlackCommand $command): BotResponseDto
    {
        $data = $this->mineInteractivityDataFromCommand($command->getText());
        $this->slackInputLogger->debug('slack command timeoff-btn-add data', [$data]);

        $startDate = $data->states->offsetGet('timeoff-start-date')->date;
        $endDate = $data->states->offsetGet('timeoff-end-date')->date;

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

        $this->sendCommandAnswer($command, $answer->text);

        return $answer;
    }

    protected function remove(SlackCommand $command): BotResponseDto
    {
        $data = $this->mineInteractivityDataFromCommand($command->getText());
        $this->slackInputLogger->debug(
            sprintf('slack command %s data', CommandName::TimeOffBtnRemove->value),
            [$data]
        );

        $text = 'Unknown time off to remove.';

        /** @var ButtonActionElement $action */
        foreach ($data->actions as $action) {
            if (CommandName::TimeOffBtnRemove->value === $action->getActionId()) {
                $timeOff = $this->entityManager->getRepository(UserTimeOff::class)->find($action->getValue());
                if ($timeOff instanceof UserTimeOff) {
                    $this->entityManager->remove($timeOff);
                    $this->entityManager->flush();

                    $text = 'Removed: ' . $this->convertTimeOffToString($timeOff);
                }
            }
        };

        $answer = new BotResponseDto($text);

        $this->show($command);

        return $answer;
    }

    protected function show(SlackCommand $command): BotResponseDto
    {
        $timeOffCollection = $this->entityManager->getRepository(UserTimeOff::class)
            ->findBy([
                'user' => $command->getUser(),
            ]);

        $answer = new BotResponseDto('');

        $options = new SlackOptions();

        $slackOptions = $options
            ->block(new SlackDividerBlock())
            ->block(new SlackHeaderBlock('Time Off that you have:'));

        foreach ($timeOffCollection as $timeOff) {
            $text = $this->convertTimeOffToString($timeOff);

            $slackOptions->block(
                (new SlackSectionBlock())
                    ->text($text)
                    ->accessory(
                        new SlackTextWithActionButtonBlockElement(
                            'remove',
                            'timeoff-btn-remove',
                            $timeOff->getRawId(),
                            'danger'
                        )
                    )
            );
        }

        if (0 === count($timeOffCollection)) {
            $slackOptions->block((new SlackSectionBlock())->text('no dates yet'));
        }

        $slackOptions->block(new SlackDividerBlock());

        $this->sendCommandAnswer(
            $command,
            $answer->text,
            $slackOptions
        );

        return $answer;
    }
}
