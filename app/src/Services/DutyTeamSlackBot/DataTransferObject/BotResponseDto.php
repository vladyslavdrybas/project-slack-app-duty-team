<?php
declare(strict_types=1);

namespace App\Services\DutyTeamSlackBot\DataTransferObject;

use Symfony\Component\HttpFoundation\Response;

readonly class BotResponseDto
{
    public function __construct(
        public string $text,
        public int $code = Response::HTTP_OK
    ) {}
}
