<?php
declare(strict_types=1);

namespace App\Services\SlackBot\DataTransferObject;

use App\Services\SlackBot\Constants\StateType;
use DateTime;

class StateDto
{
    public function __construct(
        public ?string $id,
        public ?DateTime $selectedDate,
        public ?string $value,
        public ?StateType $type,
    ) {}
}
