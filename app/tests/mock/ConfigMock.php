<?php
declare(strict_types=1);

namespace App\Tests\mock;

class ConfigMock
{
    public function get(string $name): ?string
    {
        return $_SERVER[$name] ?? null;
    }
}
