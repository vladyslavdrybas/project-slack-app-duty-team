<?php
declare(strict_types=1);

namespace App\Services\DutyTeamSlackBot\DataTransferObject\Command;

use Symfony\Component\Serializer\Annotation\SerializedName;

class AddSkillsCommandDto
{
    public function __construct(
        #[SerializedName('team_id')]
        readonly public string $teamId,
        #[SerializedName('team_domain')]
        readonly public string $teamDomain,
        #[SerializedName('channel_id')]
        readonly public string $channelId,
        #[SerializedName('channel_name')]
        readonly public string $channelName,
        #[SerializedName('user_id')]
        readonly public string $userId,
        #[SerializedName('user_name')]
        readonly public string $userName,
        #[SerializedName('command')]
        readonly public string $command,
        #[SerializedName('text')]
        readonly public string $text,
        #[SerializedName('api_app_id')]
        readonly public string $apiAppId,
        #[SerializedName('trigger_id')]
        readonly public string $triggerId,
    ){}
}
