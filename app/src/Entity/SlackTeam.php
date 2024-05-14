<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

class SlackTeam extends AbstractEntity
{
    #[ORM\Column( name: "team_id", type: Types::STRING, length: 180, unique: true, nullable: false)]
    protected string $teamId;

    #[ORM\Column( name: "team_domain", type: Types::STRING, length: 180, unique: false, nullable: false)]
    protected string $teamDomain;

    public function getTeamId(): string
    {
        return $this->teamId;
    }

    public function setTeamId(string $teamId): void
    {
        $this->teamId = $teamId;
    }

    public function getTeamDomain(): string
    {
        return $this->teamDomain;
    }

    public function setTeamDomain(string $teamDomain): void
    {
        $this->teamDomain = $teamDomain;
    }
}
