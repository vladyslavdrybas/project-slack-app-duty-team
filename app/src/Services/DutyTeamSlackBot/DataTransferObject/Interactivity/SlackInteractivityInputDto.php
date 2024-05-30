<?php
declare(strict_types=1);

namespace App\Services\DutyTeamSlackBot\DataTransferObject\Interactivity;

use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Serializer\Annotation\SerializedPath;

readonly class SlackInteractivityInputDto
{
    public function __construct(
        public string $type,

        public string $token,

        #[SerializedName('api_app_id')]
        public string $apiAppId,

        #[SerializedName('trigger_id')]
        public string $triggerId,

        #[SerializedPath('[team][id]')]
        public string $teamId,

        #[SerializedPath('[team][domain]')]
        public string $teamDomain,

        #[SerializedPath('[user][id]')]
        public string $userId,

        #[SerializedPath('[user][username]')]
        public string $userName,

        #[SerializedPath('[state][values]')]
        public array $states,

        #[SerializedName('actions')]
        public array $actions,

        #[SerializedPath('[container][type]')]
        public string $containerType,

        #[SerializedPath('[container][view_id]')]
        public ?string $containerSourceId,

        #[SerializedPath('[container][channel_id]')]
        public ?string $channelId,
    ){
    }
}
