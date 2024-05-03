<?php

namespace App\DataTransferObject;

use DateTimeInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\SerializedName;

class AccessTokenDto implements IDataTransferObject
{
    public function __construct(
        #[Assert\NotBlank]
        #[SerializedName('access_token')]
        readonly public string $accessToken,
        readonly public DateTimeInterface|string|null $expires = null,
        readonly public ?string $refreshToken = null,
        readonly public ?string $scope = null,
        #[SerializedName('token_type')]
        readonly public ?string $tokenType = null,
    ) {}
}
