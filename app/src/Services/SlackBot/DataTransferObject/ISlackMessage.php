<?php
declare(strict_types=1);

namespace App\Services\SlackBot\DataTransferObject;

interface ISlackMessage
{
    public function getToken(): string;
    public function getType(): string;
}
