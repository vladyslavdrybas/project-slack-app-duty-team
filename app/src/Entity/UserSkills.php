<?php
declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserSkillsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserSkillsRepository::class, readOnly: false)]
#[ORM\Table(name: "slack_user_skills")]
class UserSkills extends AbstractEntity
{
    #[ORM\OneToOne(targetEntity: SlackUser::class)]
    #[ORM\JoinColumn(name:'slack_user_id', referencedColumnName: 'id', nullable: false)]
    protected SlackUser $slackUser;

    #[ORM\Column( name: "skills", type: Types::JSON)]
    protected array $skills = [];

    public function getSlackUser(): SlackUser
    {
        return $this->slackUser;
    }

    public function setSlackUser(SlackUser $slackUser): void
    {
        $this->slackUser = $slackUser;
    }

    public function getSkills(): array
    {
        return $this->skills;
    }

    public function setSkills(array $skills): void
    {
        $this->skills = $skills;
    }
}
