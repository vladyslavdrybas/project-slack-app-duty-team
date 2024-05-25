<?php
declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserSkillsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserSkillsRepository::class, readOnly: false)]
#[ORM\Table(name: "user_skills")]
class UserSkills extends AbstractEntity
{
    #[ORM\OneToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name:'owner_id', referencedColumnName: 'id', nullable: false)]
    protected User $owner;

    #[ORM\Column( name: "skills", type: Types::JSON)]
    protected array $skills = [];

    public function getOwner(): User
    {
        return $this->owner;
    }

    public function setOwner(User $owner): void
    {
        $this->owner = $owner;
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
