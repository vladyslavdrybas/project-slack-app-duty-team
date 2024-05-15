<?php
declare(strict_types=1);

namespace App\Entity;

use App\Repository\SlackUserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SlackUserRepository::class, readOnly: false)]
#[ORM\Table(name: "slack_user")]
class SlackUser extends AbstractEntity
{
    #[ORM\Column( name: "user_id", type: Types::STRING, length: 180, unique: true, nullable: false)]
    protected string $userId;

    #[ORM\Column( name: "user_name", type: Types::STRING, length: 180, unique: false, nullable: false)]
    protected string $userName;

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function setUserId(string $userId): void
    {
        $this->userId = $userId;
    }

    public function getUserName(): string
    {
        return $this->userName;
    }

    public function setUserName(string $userName): void
    {
        $this->userName = $userName;
    }
}
