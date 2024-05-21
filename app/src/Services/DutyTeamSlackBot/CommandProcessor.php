<?php
declare(strict_types=1);

namespace App\Services\DutyTeamSlackBot;

use App\Entity\SlackCommand;
use App\Services\DutyTeamSlackBot\DataTransferObject\BotResponseDto;

class CommandProcessor
{
    public function __construct(
        protected readonly SkillsCommandProcessor $skillsCommandProcessor,
        protected readonly TimeOffCommandProcessor $timeOffCommandProcessor,
    ) {
    }

    public function process(SlackCommand $command): BotResponseDto
    {
        return match (true) {
            $command->getCommandName()->isSkillsCommand() => $this->skillsCommandProcessor->process($command),
            $command->getCommandName()->isTimeOffCommand() => $this->timeOffCommandProcessor->process($command),
            default => throw new \Exception('Undefined slack command stack.'),
        };
    }
}
