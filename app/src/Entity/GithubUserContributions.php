<?php

namespace App\Entity;

use App\Repository\GithubUserContributionsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: GithubUserContributionsRepository::class)]
#[ORM\Table(name: "github_user_contributions")]
#[UniqueEntity(
    fields: ['owner', 'year'],
    message: 'There is already an account with this year.'
)]
#[ORM\UniqueConstraint(
    name: 'owner_year_idx',
    columns: ['owner_id', 'year']
)]
class GithubUserContributions extends AbstractEntity
{
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name:'owner_id', referencedColumnName: 'id', nullable: false)]
    protected User $owner;

    #[ORM\Column(name: "year", type: Types::INTEGER, nullable: false)]
    protected int $year;

    #[ORM\Column(name: "total", type: Types::INTEGER, nullable: false, options: ['default' => 0])]
    protected int $total = 0;

    #[ORM\Column(nullable: true)]
    protected ?array $weeks = null;

    #[ORM\Column(nullable: true)]
    protected ?array $metadata = null;

    /**
     * @return User
     */
    public function getOwner(): User
    {
        return $this->owner;
    }

    /**
     * @param User $owner
     */
    public function setOwner(User $owner): void
    {
        $this->owner = $owner;
    }

    /**
     * @return int
     */
    public function getYear(): int
    {
        return $this->year;
    }

    /**
     * @param int $year
     */
    public function setYear(int $year): void
    {
        $this->year = $year;
    }

    /**
     * @return int
     */
    public function getTotal(): int
    {
        return $this->total;
    }

    /**
     * @param int $total
     */
    public function setTotal(int $total): void
    {
        $this->total = $total;
    }

    /**
     * @return array|null
     */
    public function getWeeks(): ?array
    {
        return $this->weeks;
    }

    /**
     * @param array|null $weeks
     */
    public function setWeeks(?array $weeks): void
    {
        $this->weeks = $weeks;
    }

    /**
     * @return array|null
     */
    public function getMetadata(): ?array
    {
        return $this->metadata;
    }

    /**
     * @param array|null $metadata
     */
    public function setMetadata(?array $metadata): void
    {
        $this->metadata = $metadata;
    }
}
