<?php
declare(strict_types=1);

namespace App\Services\SlackBot\DataTransferObject;

use Symfony\Component\Serializer\Annotation\SerializedName;

class TeamDto
{
    public function __construct(
        #[SerializedName('id')]
        public string $id,

        #[SerializedName('domain')]
        public string $domain
    ) {}

}
