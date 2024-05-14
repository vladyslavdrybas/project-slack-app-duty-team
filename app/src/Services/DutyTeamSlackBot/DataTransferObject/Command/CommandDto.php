<?php
declare(strict_types=1);

namespace App\Services\DutyTeamSlackBot\DataTransferObject\Command;

use App\Services\DutyTeamSlackBot\Config\CommandList;
use App\Services\DutyTeamSlackBot\DataTransferObject\ChannelDto;
use App\Services\DutyTeamSlackBot\DataTransferObject\TeamDto;
use App\Services\DutyTeamSlackBot\DataTransferObject\UserDto;

readonly class CommandDto
{
    public function __construct(
        public string $token,
        public TeamDto $team,
        public ChannelDto $channel,
        public UserDto $user,
        public CommandList $command,
        public string $text,
        public string $apiAppId,
        public string $triggerId
    ) {}
}
