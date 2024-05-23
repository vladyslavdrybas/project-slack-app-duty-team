<?php
declare(strict_types=1);

namespace App\Services\DutyTeamSlackBot\DataTransferObject\Interactivity;

readonly class ButtonActionElement implements IActionElement, IButtonElement
{
    public string $type;
    public function __construct(
        public string $actionId,
        public string $value,
        public string $text
    ) {
        $this->type = 'button';
    }

    public function getActionId(): string
    {
        return $this->actionId;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getText(): string
    {
        return $this->text;
    }
}
