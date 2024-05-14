<?php
declare(strict_types=1);

namespace App\Services\DutyTeamSlackBot\DataTransferObject;

use Symfony\Component\Serializer\Annotation\SerializedName;

class TeamDto
{
    public function __construct(
        #[SerializedName('team_id')]
        public string $teamId,
        #[SerializedName('team_domain')]
        public string $teamDomain
    ) {}

}
