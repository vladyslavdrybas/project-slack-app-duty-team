<?php
declare(strict_types=1);

namespace App\Services\SlackBot\DataTransferObject;

use Symfony\Component\Serializer\Annotation\SerializedName;

class UserDto
{
    public function __construct(
        public string $id,

        public ?string $name,

        public ?string $username,

        #[SerializedName('team_id')]
        public ?string $teamId,
    ) {}
}
