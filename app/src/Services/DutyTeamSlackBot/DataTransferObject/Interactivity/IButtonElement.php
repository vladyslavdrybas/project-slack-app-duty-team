<?php
declare(strict_types=1);

namespace App\Services\DutyTeamSlackBot\DataTransferObject\Interactivity;

interface IButtonElement
{
    public function getValue(): string;
    public function getText(): string;
}
