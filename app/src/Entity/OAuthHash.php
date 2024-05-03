<?php

namespace App\Entity;

use App\Repository\OAuthHashRepository;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OAuthHashRepository::class, readOnly: false)]
#[ORM\Table(name: "oauth_hash")]
class OAuthHash implements EntityInterface
{
    #[ORM\Id]
    #[ORM\OneToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name:'owner_id', referencedColumnName: 'id', nullable: false)]
    protected User $owner;

    #[ORM\Id]
    #[ORM\Column(name: "hash", type: Types::STRING, length: 64, unique: true, nullable: false)]
    protected string $hash;

    #[ORM\Column(name: "expire_at", type: Types::DATETIME_MUTABLE, nullable: false)]
    protected DateTimeInterface $expireAt;

    /**
     * @return string
     */
    public function getObject(): string
    {
        $namespace = explode('\\', static::class);

        return array_pop($namespace);
    }

    /**
     * @return string
     */
    public function getRawId(): string
    {
        return $this->getHash() . ':' . $this->getOwner()->getRawId();
    }

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
     * @return string
     */
    public function getHash(): string
    {
        return $this->hash;
    }

    /**
     * @param string $hash
     */
    public function setHash(string $hash): void
    {
        $this->hash = $hash;
    }

    /**
     * @return DateTimeInterface
     */
    public function getExpireAt(): DateTimeInterface
    {
        return $this->expireAt;
    }

    /**
     * @param DateTimeInterface $expireAt
     */
    public function setExpireAt(DateTimeInterface $expireAt): void
    {
        $this->expireAt = $expireAt;
    }
}
