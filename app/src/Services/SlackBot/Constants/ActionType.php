<?php
declare(strict_types=1);

namespace App\Services\SlackBot\Constants;

enum ActionType: string
{
    case Button = 'button';

    public function isButton(): bool
    {
        return $this === self::Button;
    }
}
