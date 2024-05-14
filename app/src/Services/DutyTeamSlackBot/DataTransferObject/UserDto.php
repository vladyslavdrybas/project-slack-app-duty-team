<?php
declare(strict_types=1);

namespace App\Services\DutyTeamSlackBot\DataTransferObject;

use Symfony\Component\Serializer\Annotation\SerializedName;

class UserDto
{
    public function __construct(
        #[SerializedName('user_id')]
        public string $userId,
        #[SerializedName('user_name')]
        public string $userName
    ) {}
}
