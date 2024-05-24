<?php
declare(strict_types=1);

namespace App\Services\DutyTeamSlackBot\DataTransferObject\Interactivity;

use App\Services\DutyTeamSlackBot\DataTransferObject\ChannelDto;
use App\Services\DutyTeamSlackBot\DataTransferObject\Interactivity\Blocks\ActionCollection;
use App\Services\DutyTeamSlackBot\DataTransferObject\Interactivity\Blocks\StateCollection;
use App\Services\DutyTeamSlackBot\DataTransferObject\ISlackMessageIdentifier;
use App\Services\DutyTeamSlackBot\DataTransferObject\TeamDto;
use App\Services\DutyTeamSlackBot\DataTransferObject\UserDto;

readonly class InteractivityDto implements ISlackMessageIdentifier
{
    public function __construct(
        public string       $token,
        public string       $apiAppId,
        public string       $triggerId,
        public TeamDto      $team,
        public ChannelDto   $channel,
        public UserDto      $user,
        public string       $type,
        public StateCollection $states,
        public ActionCollection $actions
    ) {}

    public function getToken(): string
    {
        return $this->token;
    }

    public function getApiAppId(): string
    {
        return $this->apiAppId;
    }

    public function getTriggerId(): string
    {
        return $this->triggerId;
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

    public function getType(): string
    {
        return $this->type;
    }

    public function getStates(): StateCollection
    {
        return $this->states;
    }

    /**
     * @return ActionCollection
     */
    public function getActions(): ActionCollection
    {
        return $this->actions;
    }
}
