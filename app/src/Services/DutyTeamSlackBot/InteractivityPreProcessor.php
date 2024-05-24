<?php
declare(strict_types=1);

namespace App\Services\DutyTeamSlackBot;

use App\Entity\SlackCommand;
use App\Services\DutyTeamSlackBot\Config\CommandList;
use App\Services\DutyTeamSlackBot\DataTransferObject\Interactivity\IActionElement;
use App\Services\DutyTeamSlackBot\DataTransferObject\Interactivity\InteractivityDto;
use App\Services\DutyTeamSlackBot\DataTransferObject\ISlackMessageIdentifier;

class InteractivityPreProcessor extends AbstractPreProcessor
{
    public function process(ISlackMessageIdentifier $dto): SlackCommand
    {
        if (!$dto instanceof InteractivityDto) {
            throw new \Exception("Invalid DTO.");
        }

        return parent::process($dto);
    }

    protected function getCommandName(ISlackMessageIdentifier|InteractivityDto $dto): CommandList
    {
        $command = null;

        foreach ($dto->getActions() as $action) {
            /** @var IActionElement $action */
            $command = CommandList::tryFrom($action->getActionId());
            if (null !== $command) {
                break;
            }
        }

        if (!$command instanceof CommandList) {
            throw new \Exception("Cannot get action command");
        }

        return $command;
    }

    protected function getText(ISlackMessageIdentifier|InteractivityDto $dto): string
    {
        return json_encode([
            'type' => $dto->type,
            'states' => $dto->states->getArrayCopy(),
            'actions' => $dto->actions->getArrayCopy(),
        ]);
    }
}
