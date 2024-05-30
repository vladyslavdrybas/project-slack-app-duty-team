<?php
declare(strict_types=1);

namespace App\Services\SlackBot\DataTransferObject;

use App\Services\SlackBot\Constants\ActionType;

class ActionCollection extends \ArrayObject
{
    public function __construct(
        object|array $array = [],
        int $flags = 0,
        string $iteratorClass = "ArrayIterator"
    ) {
        $newArray = [];
        foreach ($array as $item) {
            $action = new ActionDto(
                $item['action_id'] ?? null,
                $item['block_id'] ?? null,
                $item['text']['text'] ?? null,
                $item['value'] ?? null,
                $item['style'] ?? null,
                ActionType::tryFrom($item['type'] ?? ''),
            );

            $newArray[$action->id] = $action;
        }

        parent::__construct($newArray, $flags, $iteratorClass);
    }

    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }
}
