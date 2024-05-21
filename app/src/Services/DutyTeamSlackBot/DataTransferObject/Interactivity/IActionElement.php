<?php
declare(strict_types=1);

namespace App\Services\DutyTeamSlackBot\DataTransferObject\Interactivity;

interface IActionElement
{
    public function getActionId(): string;
    public function getType(): string;
}
