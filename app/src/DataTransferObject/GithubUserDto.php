<?php

namespace App\DataTransferObject;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\SerializedName;

class GithubUserDto implements IDataTransferObject
{
    /** @param array<EmailDto> $emails */
    public function __construct(
        #[Assert\NotBlank]
        readonly public int $id,
        #[Assert\NotBlank]
        readonly public string $login,
        #[SerializedName('node_id')]
        readonly public string $nodeId,
        #[SerializedName('avatar_url')]
        readonly public ?string $avatarUrl,
        readonly public array $emails,
        public ?string $email,
        readonly public ?string $url,
        readonly public ?string $name,
        readonly public ?string $location,
        readonly public ?string $company,
        readonly public ?string $blog,
        #[SerializedName('twitter_username')]
        readonly public ?string $twitterUsername,
        #[SerializedName('public_repos')]
        readonly public ?int $publicRepos,
        #[SerializedName('total_private_repos')]
        readonly public ?int $totalPrivateRepos,
        #[SerializedName('owned_private_repos')]
        readonly public ?int $ownedPrivateRepos,
        #[SerializedName('disk_usage')]
        readonly public ?int $diskUsage,
        readonly public ?int $collaborators,
        readonly public ?int $followers,
        readonly public ?int $following,
        #[SerializedName('two_factor_authentication')]
        readonly public ?bool $twoFactorAuthentication
    ) {}
}
