<?php
declare(strict_types=1);

namespace App\Services\DutyTeamSlackBot\DataTransferObject\Command;

use App\Services\DutyTeamSlackBot\Config\CommandList;
use App\Services\DutyTeamSlackBot\DataTransferObject\ChannelDto;
use App\Services\DutyTeamSlackBot\DataTransferObject\ISlackMessageIdentifier;
use App\Services\DutyTeamSlackBot\DataTransferObject\TeamDto;
use App\Services\DutyTeamSlackBot\DataTransferObject\UserDto;

readonly class CommandDto implements ISlackMessageIdentifier
{
    public function __construct(
        public string $token,
        public TeamDto $team,
        public ChannelDto $channel,
        public UserDto $user,
        public string $text,
        public string $apiAppId,
        public string $triggerId,
        public ?CommandList $command = null
    ) {}

    public function getToken(): string
    {
        return $this->token;
    }

    public function getTeam(): TeamDto
    {
        return $this->team;
    }

    public function getChannel(): ChannelDto
    {
        return $this->channel;
    }

    public function getUser(): UserDto
    {
        return $this->user;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getApiAppId(): string
    {
        return $this->apiAppId;
    }

    public function getTriggerId(): string
    {
        return $this->triggerId;
    }

    public function getCommand(): ?CommandList
    {
        return $this->command;
    }
}
