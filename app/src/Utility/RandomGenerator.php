<?php

declare(strict_types=1);

namespace App\Utility;

use function bin2hex;
use function hash;
use function random_bytes;
use function sprintf;
use function time;
use function uniqid;

class RandomGenerator
{
    public function uniqueId(string $prefix = ''): string
    {
        return sprintf(
            '%s.%s'
            , uniqid($prefix, false)
            , bin2hex(random_bytes(3))
        );
    }

    public function sha256(string $salt = ''): string
    {
        return hash('sha256',bin2hex(random_bytes(13)) . $salt . time());
    }
}
