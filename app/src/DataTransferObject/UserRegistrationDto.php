<?php

declare(strict_types=1);

namespace App\DataTransferObject;

use Symfony\Component\Validator\Constraints as Assert;

class UserRegistrationDto implements IDataTransferObject
{
    public function __construct(
        #[Assert\Email]
        #[Assert\NotBlank]
        public string $email,

        #[Assert\NotBlank]
        public string $password
    ) {
    }
}
