<?php
declare(strict_types=1);

namespace App\Services\DutyTeamSlackBot\DataTransferObject;

use Symfony\Component\Serializer\Annotation\SerializedName;

class ChannelDto
{
    public function __construct(
        #[SerializedName('channel_id')]
        public string $channelId,
        #[SerializedName('channel_name')]
        public string $channelName
    ) {}
}
