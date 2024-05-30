<?php
declare(strict_types=1);

namespace App\Services\SlackBot\Constants;

enum MessageType: string
{
    case EventCallback = 'event_callback';
    case UrlVerification = 'url_verification';
    case BlockActions = 'block_actions';

    public function isEventCallback(): bool
    {
        return $this === self::EventCallback;
    }

    public function isUrlVerification(): bool
    {
        return $this === self::UrlVerification;
    }

    public function isBlockActions(): bool
    {
        return $this === self::BlockActions;
    }
}
