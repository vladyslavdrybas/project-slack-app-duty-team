<?php

namespace App\Entity;

use App\Repository\GithubAccessTokenRepository;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: GithubAccessTokenRepository::class, readOnly: false)]
#[ORM\Table(name: "github_access_token")]
#[UniqueEntity(
    fields: ['owner', 'email', 'username'],
    message: 'There is already an account with this email connected to the user.'
)]
#[ORM\UniqueConstraint(
    name: 'owner_email_username_idx',
    columns: ['owner_id', 'email', 'username']
)]
class GithubAccessToken extends AbstractEntity
{
    #[ORM\Column(name: "access_token", type: Types::STRING, length: 256, unique: true, nullable: false)]
    protected string $accessToken;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name:'owner_id', referencedColumnName: 'id', nullable: false)]
    protected User $owner;

    #[Assert\Email]
    #[Assert\NotBlank]
    #[ORM\Column( name: "email", type: Types::STRING, length: 200, unique: false, nullable: false)]
    protected string $email;

    #[Assert\NotBlank]
    #[ORM\Column( name: "username", type: Types::STRING, length: 200, unique: false, nullable: false)]
    protected string $username;

    #[ORM\Column( name: "user_id", type: Types::STRING, length: 200, unique: false, nullable: true)]
    protected ?string $userId = null;

    #[ORM\Column( name: "firstname", type: Types::STRING, length: 200, nullable: true)]
    protected ?string $firstname = null;

    #[ORM\Column( name: "lastname", type: Types::STRING, length: 200, nullable: true)]
    protected ?string $lastname = null;

    #[ORM\Column(name: "expire_at", type: Types::DATETIME_MUTABLE, nullable: true)]
    protected ?DateTimeInterface $expireAt = null;

    #[ORM\Column( name: "metadata", type: Types::JSON)]
    protected array $metadata;

    /**
     * @return string
     */
    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    /**
     * @param string $accessToken
     */
    public function setAccessToken(string $accessToken): void
    {
        $this->accessToken = $accessToken;
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
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    /**
     * @return string|null
     */
    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    /**
     * @param string|null $firstname
     */
    public function setFirstname(?string $firstname): void
    {
        $this->firstname = $firstname;
    }

    /**
     * @return string|null
     */
    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    /**
     * @param string|null $lastname
     */
    public function setLastname(?string $lastname): void
    {
        $this->lastname = $lastname;
    }

    /**
     * @return array
     */
    public function getMetadata(): array
    {
        return $this->metadata;
    }

    /**
     * @param array $metadata
     */
    public function setMetadata(array $metadata): void
    {
        $this->metadata = $metadata;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getExpireAt(): ?DateTimeInterface
    {
        return $this->expireAt;
    }

    /**
     * @param DateTimeInterface|null $expireAt
     */
    public function setExpireAt(?DateTimeInterface $expireAt): void
    {
        $this->expireAt = $expireAt;
    }

    /**
     * @return string|null
     */
    public function getUserId(): ?string
    {
        return $this->userId;
    }

    /**
     * @param string|null $userId
     */
    public function setUserId(?string $userId): void
    {
        $this->userId = $userId;
    }
}
