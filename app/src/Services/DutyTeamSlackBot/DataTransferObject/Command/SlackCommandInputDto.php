<?php
declare(strict_types=1);

namespace App\Services\DutyTeamSlackBot\DataTransferObject\Command;

use Symfony\Component\Serializer\Annotation\SerializedName;

readonly class SlackCommandInputDto
{
    public function __construct(
        public string $token,
        #[SerializedName('team_id')]
        public string $teamId,
        #[SerializedName('team_domain')]
        public string $teamDomain,
        #[SerializedName('channel_id')]
        public string $channelId,
        #[SerializedName('channel_name')]
        public string $channelName,
        #[SerializedName('user_id')]
        public string $userId,
        #[SerializedName('user_name')]
        public string $userName,
        #[SerializedName('command')]
        public string $command,
        #[SerializedName('text')]
        public string $text,
        #[SerializedName('api_app_id')]
        public string $apiAppId,
        #[SerializedName('trigger_id')]
        public string $triggerId
    ){
    }
}
