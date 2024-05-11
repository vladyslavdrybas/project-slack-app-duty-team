<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface as SecurityUserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

use function array_unique;

# TODO add user password reset
# TODO add user email confirmation
# TODO add user email (unique identifier) change/update
# TODO add user subscriptions
# TODO add payments
# TODO add mail promotions for user
# TODO add web push notification promotions for user
# TODO add web/email reminders for user to do some action
#[ORM\Entity(repositoryClass: UserRepository::class, readOnly: false)]
#[ORM\Table(name: "user")]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email.')]
class User extends AbstractEntity implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Column( name: "roles", type: Types::JSON, nullable: false )]
    protected array $roles = [self::ROLE_USER];

    #[Assert\Email]
    #[Assert\NotBlank]
    #[ORM\Column( name: "email", type: Types::STRING, length: 180, unique: true, nullable: false )]
    protected string $email = '';

    #[ORM\Column( name: "username", type: Types::STRING, length: 100, unique: true, nullable: false)]
    protected string $username = '';

    #[ORM\Column( name: "firstname", type: Types::STRING, length: 100, nullable: true)]
    protected ?string $firstname = null;

    #[ORM\Column( name: "lastname", type: Types::STRING, length: 100, nullable: true)]
    protected ?string $lastname = null;

    #[ORM\Column(name: "password", type: Types::STRING, length: 100, unique: false, nullable: false)]
    protected string $password = '';

    #[ORM\Column( name: "is_email_verified", type: 'boolean', options: ["default" => false] )]
    protected bool $isEmailVerified = false;

    #[ORM\Column(name: "is_active", type: Types::BOOLEAN, options: ["default" => true])]
    protected bool $isActive = true;

    #[ORM\Column(name: "is_banned", type: Types::BOOLEAN, options: ["default" => false])]
    protected bool $isBanned = false;

    #[ORM\Column(name: "is_deleted", type: Types::BOOLEAN, options: ["default" => false])]
    protected bool $isDeleted = false;

    public function isEqualTo(SecurityUserInterface $user): bool
    {
        return $user->getUserIdentifier() === $this->getUserIdentifier();
    }

    public function addRole(string $role): void
    {
        $this->roles[] = $role;
        $this->setRoles($this->roles);
    }

    protected function setRoles(array $roles): void
    {
        $this->roles = array_unique($roles);
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

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

    public function setUserIdentifier(string $identifier): void
    {
        $this->setEmail($identifier);
    }

    public function eraseCredentials(): void
    {
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
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
     * @return bool
     */
    public function isEmailVerified(): bool
    {
        return $this->isEmailVerified;
    }

    /**
     * @param bool $isEmailVerified
     */
    public function setIsEmailVerified(bool $isEmailVerified): void
    {
        $this->isEmailVerified = $isEmailVerified;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->isActive;
    }

    /**
     * @param bool $isActive
     */
    public function setIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
    }

    /**
     * @return bool
     */
    public function isBanned(): bool
    {
        return $this->isBanned;
    }

    /**
     * @param bool $isBanned
     */
    public function setIsBanned(bool $isBanned): void
    {
        $this->isBanned = $isBanned;
    }

    /**
     * @return bool
     */
    public function isDeleted(): bool
    {
        return $this->isDeleted;
    }

    /**
     * @param bool $isDeleted
     */
    public function setIsDeleted(bool $isDeleted): void
    {
        $this->isDeleted = $isDeleted;
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
}
