<?php
declare(strict_types=1);

namespace App\Services\SlackBot\DataTransferObject;

use App\Services\SlackBot\Constants\ActionType;
use Symfony\Component\Serializer\Annotation\SerializedName;

class ActionDto
{
    public function __construct(
        #[SerializedName('action_id')]
        public ?string $id,
        #[SerializedName('block_id')]
        public ?string $blockId,
        public ?string $text,
        public ?string $value,
        public ?string $style,
        public ?ActionType $type,
    ) {}
}
