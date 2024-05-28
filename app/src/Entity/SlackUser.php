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
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name:'owner_id', referencedColumnName: 'id', nullable: false)]
    protected User $owner;

    #[ORM\Column( name: 'user_id', type: Types::STRING, length: 100, unique: true, nullable: false)]
    protected string $userId;

    #[ORM\Column( name: 'user_name', type: Types::STRING, length: 255, unique: false, nullable: false)]
    protected string $userName;

    #[ORM\Column( name: 'team_id', type: Types::STRING, length: 100, unique: true, nullable: false)]
    protected string $teamId;

    #[ORM\Column( name: 'team_domain', type: Types::STRING, length: 255, unique: false, nullable: false)]
    protected string $teamDomain;

    #[ORM\Column( name: 'email', type: Types::STRING, length: 255, nullable: true )]
    protected ?string  $email = null;

    #[ORM\Column( name: 'full_name', type: Types::STRING, length: 255, nullable: true)]
    protected ?string $fullName = null;

    #[ORM\Column( name: 'first_name', type: Types::STRING, length: 255, nullable: true)]
    protected ?string $firstName = null;

    #[ORM\Column( name: 'last_name', type: Types::STRING, length: 255, nullable: true)]
    protected ?string $lastName = null;

    #[ORM\Column(name: 'timezone', type: Types::STRING, length: 255, nullable: true)]
    protected ?string $timezone = null;

    #[ORM\Column(name: 'timezone_label', type: Types::STRING, length: 255, nullable: true)]
    protected ?string $timezoneLabel = null;

    #[ORM\Column(name: 'timezone_offset', type: Types::INTEGER, options: ['default' => 0])]
    protected int $timezoneOffset = 0;

    #[ORM\Column(name: 'title', type: Types::STRING, length: 255, nullable: true)]
    protected ?string $title = null;

    #[ORM\Column(name: 'phone', type: Types::STRING, length: 255, nullable: true)]
    protected ?string $phone = null;

    #[ORM\Column(name: 'skype', type: Types::STRING, length: 255, nullable: true)]
    protected ?string $skype = null;

    #[ORM\Column(name: 'avatar', type: Types::STRING, length: 255, nullable: true)]
    protected ?string $avatar = null;

    #[ORM\Column(name: 'avatar_hash', type: Types::STRING, length: 100, nullable: true)]
    protected ?string $avatarHash = null;

    #[ORM\Column(name: 'color', type: Types::STRING, length: 6, nullable: true)]
    protected ?string $color = null;

    #[ORM\Column(name: 'is_deleted', type: Types::BOOLEAN, options: ['default' => false])]
    protected bool $isDeleted = false;

    #[ORM\Column(name: 'is_admin', type: Types::BOOLEAN, options: ['default' => false])]
    protected bool $isAdmin = false;

    #[ORM\Column(name: 'is_owner', type: Types::BOOLEAN, options: ['default' => false])]
    protected bool $isOwner = false;

    #[ORM\Column(name: 'is_primary_owner', type: Types::BOOLEAN, options: ['default' => false])]
    protected bool $isPrimaryOwner = false;

    #[ORM\Column(name: 'is_restricted', type: Types::BOOLEAN, options: ['default' => false])]
    protected bool $isRestricted = false;

    #[ORM\Column(name: 'is_ultra_restricted', type: Types::BOOLEAN, options: ['default' => false])]
    protected bool $isUltraRestricted = false;

    #[ORM\Column(name: 'is_bot', type: Types::BOOLEAN, options: ['default' => false])]
    protected bool $isBot = false;

    #[ORM\Column(name: 'is_app_user', type: Types::BOOLEAN, options: ['default' => false])]
    protected bool $isAppUser = false;

    #[ORM\Column(name: 'is_email_confirmed', type: Types::BOOLEAN, options: ['default' => false])]
    protected bool $isEmailConfirmed = false;

    #[ORM\Column(name: 'has_view', type: Types::BOOLEAN, options: ['default' => false])]
    protected bool $hasView = false;

    public function getOwner(): User
    {
        return $this->owner;
    }

    public function setOwner(User $owner): void
    {
        $this->owner = $owner;
    }

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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(?string $fullName): void
    {
        $this->fullName = $fullName;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getTimezone(): ?string
    {
        return $this->timezone;
    }

    public function setTimezone(?string $timezone): void
    {
        $this->timezone = $timezone;
    }

    public function getTimezoneLabel(): ?string
    {
        return $this->timezoneLabel;
    }

    public function setTimezoneLabel(?string $timezoneLabel): void
    {
        $this->timezoneLabel = $timezoneLabel;
    }

    public function getTimezoneOffset(): int
    {
        return $this->timezoneOffset;
    }

    public function setTimezoneOffset(int $timezoneOffset): void
    {
        $this->timezoneOffset = $timezoneOffset;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): void
    {
        $this->phone = $phone;
    }

    public function getSkype(): ?string
    {
        return $this->skype;
    }

    public function setSkype(?string $skype): void
    {
        $this->skype = $skype;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): void
    {
        $this->avatar = $avatar;
    }

    public function getAvatarHash(): ?string
    {
        return $this->avatarHash;
    }

    public function setAvatarHash(?string $avatarHash): void
    {
        $this->avatarHash = $avatarHash;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): void
    {
        $this->color = $color;
    }

    public function isDeleted(): bool
    {
        return $this->isDeleted;
    }

    public function setIsDeleted(bool $isDeleted): void
    {
        $this->isDeleted = $isDeleted;
    }

    public function isAdmin(): bool
    {
        return $this->isAdmin;
    }

    public function setIsAdmin(bool $isAdmin): void
    {
        $this->isAdmin = $isAdmin;
    }

    public function isOwner(): bool
    {
        return $this->isOwner;
    }

    public function setIsOwner(bool $isOwner): void
    {
        $this->isOwner = $isOwner;
    }

    public function isPrimaryOwner(): bool
    {
        return $this->isPrimaryOwner;
    }

    public function setIsPrimaryOwner(bool $isPrimaryOwner): void
    {
        $this->isPrimaryOwner = $isPrimaryOwner;
    }

    public function isRestricted(): bool
    {
        return $this->isRestricted;
    }

    public function setIsRestricted(bool $isRestricted): void
    {
        $this->isRestricted = $isRestricted;
    }

    public function isUltraRestricted(): bool
    {
        return $this->isUltraRestricted;
    }

    public function setIsUltraRestricted(bool $isUltraRestricted): void
    {
        $this->isUltraRestricted = $isUltraRestricted;
    }

    public function isBot(): bool
    {
        return $this->isBot;
    }

    public function setIsBot(bool $isBot): void
    {
        $this->isBot = $isBot;
    }

    public function isAppUser(): bool
    {
        return $this->isAppUser;
    }

    public function setIsAppUser(bool $isAppUser): void
    {
        $this->isAppUser = $isAppUser;
    }

    public function isEmailConfirmed(): bool
    {
        return $this->isEmailConfirmed;
    }

    public function setIsEmailConfirmed(bool $isEmailConfirmed): void
    {
        $this->isEmailConfirmed = $isEmailConfirmed;
    }

    public function isHasView(): bool
    {
        return $this->hasView;
    }

    public function setHasView(bool $hasView): void
    {
        $this->hasView = $hasView;
    }
}
