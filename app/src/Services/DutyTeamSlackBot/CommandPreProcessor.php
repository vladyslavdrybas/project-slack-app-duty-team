<?php
declare(strict_types=1);

namespace App\Services\DutyTeamSlackBot;

use App\Entity\SlackCommand;
use App\Services\DutyTeamSlackBot\Config\CommandList;
use App\Services\DutyTeamSlackBot\DataTransferObject\Command\CommandDto;
use App\Services\DutyTeamSlackBot\DataTransferObject\ISlackMessageIdentifier;

class CommandPreProcessor extends AbstractPreProcessor
{
    public function process(ISlackMessageIdentifier $dto): SlackCommand
    {
        if (!$dto instanceof CommandDto) {
            throw new \Exception("Invalid DTO.");
        }

        return parent::process($dto);
    }

    protected function getCommandName(ISlackMessageIdentifier|CommandDto $dto): CommandList
    {
        return $dto->command;
    }

    protected function getText(ISlackMessageIdentifier|CommandDto $dto): string
    {
        return $this->cleanText($dto->text);
    }

    protected function validate(ISlackMessageIdentifier|CommandDto $dto): void
    {
        parent::validate($dto);

        if (null === $dto->getCommand()) {
            throw new \Exception("Invalid command.");
        }
    }
}
