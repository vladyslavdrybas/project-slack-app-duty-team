<?php

declare(strict_types=1);

namespace App\Exceptions;

use Throwable;

class AccessDenied extends \Exception
{
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        if (empty($message)) {
            $message = 'Access denied';
        }

        parent::__construct($message, $code, $previous);
    }
}
