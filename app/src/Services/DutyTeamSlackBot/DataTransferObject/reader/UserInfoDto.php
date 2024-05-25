<?php
declare(strict_types=1);

namespace App\Services\DutyTeamSlackBot\DataTransferObject\reader;

use Symfony\Component\Serializer\Annotation\SerializedPath;
use Symfony\Component\Serializer\Annotation\SerializedName;

readonly class UserInfoDto
{
    public function __construct(
        #[SerializedPath('[user][id]')]
        public ?string $userID,

        #[SerializedPath('[user][name]')]
        public ?string $userName,

        #[SerializedPath('[user][team_id]')]
        public ?string $teamId,

        #[SerializedPath('[user][color]')]
        public ?string $color = null,

        #[SerializedPath('[user][real_name]')]
        public ?string $fullName = null,

        #[SerializedPath('[user][tz]')]
        public ?string $timezone = null,

        #[SerializedPath('[user][tz_label]')]
        public ?string $timezoneLabel = null,

        #[SerializedPath('[user][profile][first_name]')]
        public ?string $firstName = null,

        #[SerializedPath('[user][profile][last_name]')]
        public ?string $lastName = null,

        #[SerializedPath('[user][profile][email]')]
        public ?string $email = null,

        #[SerializedPath('[user][profile][avatar_hash]')]
        public ?string $avatarHash = null,

        #[SerializedPath('[user][profile][image_512]')]
        public ?string $avatar = null,

        #[SerializedPath('[user][profile][title]')]
        public ?string $title = null,

        #[SerializedPath('[user][profile][phone]')]
        public ?string $phone = null,

        #[SerializedPath('[user][profile][skype]')]
        public ?string $skype = null,

        #[SerializedPath('[user][tz_offset]')]
        public int $timezoneOffset = 0,

        #[SerializedPath('[user][deleted]')]
        public bool $isDeleted = false,

        #[SerializedPath('[user][is_admin]')]
        public bool $isAdmin = false,

        #[SerializedPath('[user][is_owner]')]
        public bool $isOwner = false,

        #[SerializedPath('[user][is_primary_owner]')]
        public bool $isPrimaryOwner = false,

        #[SerializedPath('[user][is_restricted]')]
        public bool $isRestricted = false,

        #[SerializedPath('[user][is_ultra_restricted]')]
        public bool $isUltraRestricted = false,

        #[SerializedPath('[user][is_bot]')]
        public bool $isBot = false,

        #[SerializedPath('[user][is_app_user]')]
        public bool $isAppUser = false,

        #[SerializedPath('[user][is_email_confirmed]')]
        public bool $isEmailConfirmed = false,

        #[SerializedName('ok')]
        public bool $isSuccess = false,
    ) {}
}
