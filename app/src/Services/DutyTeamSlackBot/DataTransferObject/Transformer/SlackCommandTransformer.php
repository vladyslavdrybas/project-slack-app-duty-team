<?php
declare(strict_types=1);

namespace App\Services\DutyTeamSlackBot\DataTransferObject\Transformer;

use App\Services\DutyTeamSlackBot\Config\CommandList;
use App\Services\DutyTeamSlackBot\DataTransferObject\ChannelDto;
use App\Services\DutyTeamSlackBot\DataTransferObject\Command\CommandDto;
use App\Services\DutyTeamSlackBot\DataTransferObject\Command\SlackCommandInputDto;
use App\Services\DutyTeamSlackBot\DataTransferObject\TeamDto;
use App\Services\DutyTeamSlackBot\DataTransferObject\UserDto;

class SlackCommandTransformer
{
    public function transform(SlackCommandInputDto $input): CommandDto
    {
        $command = CommandList::tryFrom(substr($input->command, 1));

        return new CommandDto(
            $input->token,
            new TeamDto($input->teamId, $input->teamDomain),
            new ChannelDto($input->channelId, $input->channelName),
            new UserDto($input->userId, $input->userName),
            $command,
            $input->text,
            $input->apiAppId,
            $input->triggerId
        );
    }
}
