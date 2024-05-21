<?php
declare(strict_types=1);

namespace App\Services\DutyTeamSlackBot\DataTransferObject;

interface ISlackMessageIdentifier
{
    public function getUser(): UserDto;
    public function getTeam(): TeamDto;
    public function getChannel(): ChannelDto;
    public function getToken(): string;
    public function getApiAppId(): string;
}
