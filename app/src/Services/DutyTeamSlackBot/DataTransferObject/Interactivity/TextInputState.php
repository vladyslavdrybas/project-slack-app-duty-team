<?php
declare(strict_types=1);

namespace App\Services\DutyTeamSlackBot\DataTransferObject\Interactivity;

readonly class TextInputState
{
    public string $type;

    public function __construct(
        public string $value
    ) {
        $this->type = 'plain_text_input';
    }
}
