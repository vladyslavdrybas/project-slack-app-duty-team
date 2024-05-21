<?php
declare(strict_types=1);

namespace App\Services\DutyTeamSlackBot\DataTransferObject\Interactivity;

readonly class DatePickerState
{
    public string $type;

    public function __construct(
        public \DateTimeInterface $date
    ) {
        $this->type = 'datepicker';
    }
}
