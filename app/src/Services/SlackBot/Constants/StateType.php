<?php
declare(strict_types=1);

namespace App\Services\SlackBot\Constants;

enum StateType: string
{
    case PlainTextInput = 'plain_text_input';
    case Datepicker = 'datepicker';
}
