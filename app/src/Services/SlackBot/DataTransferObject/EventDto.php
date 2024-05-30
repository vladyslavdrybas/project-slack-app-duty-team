<?php
declare(strict_types=1);

namespace App\Services\SlackBot\DataTransferObject;

use Symfony\Component\Serializer\Annotation\SerializedName;

class EventDto
{
    public function __construct(
        public ?string $id,
        public ?string $type,
        public ?string $user,
        public ?string $channel,
        public ?string $tab,
        public ?ViewDto $view,
    ) {}
}
