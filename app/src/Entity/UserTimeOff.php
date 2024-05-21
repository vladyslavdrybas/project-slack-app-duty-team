<?php
declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserTimeOffRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserTimeOffRepository::class, readOnly: false)]
#[ORM\Table(name: "user_time_off")]
class UserTimeOff extends AbstractEntity
{
    #[ORM\ManyToOne(targetEntity: SlackUser::class)]
    #[ORM\JoinColumn(name:'user_id', referencedColumnName: 'id', nullable: false)]
    protected SlackUser $user;

    #[ORM\Column(name:'start_at',type: Types::DATETIME_MUTABLE)]
    protected \DateTime $startAt;

    #[ORM\Column(name:'end_at',type: Types::DATETIME_MUTABLE)]
    protected \DateTime $endAt;

    public function getUser(): SlackUser
    {
        return $this->user;
    }

    public function setUser(SlackUser $user): void
    {
        $this->user = $user;
    }

    public function getStartAt(): \DateTime
    {
        return $this->startAt;
    }

    public function setStartAt(\DateTime $startAt): void
    {
        $this->startAt = $startAt;
    }

    public function getEndAt(): \DateTime
    {
        return $this->endAt;
    }

    public function setEndAt(\DateTime $endAt): void
    {
        $this->endAt = $endAt;
    }
}
