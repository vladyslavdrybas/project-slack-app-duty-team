<?php
declare(strict_types=1);

namespace App\Services\SlackBot\DataTransferObject;

use App\Services\SlackBot\Constants\StateType;
use DateTime;

class StateCollection extends \ArrayObject
{
    public function __construct(
        object|array $array = [],
        int $flags = 0,
        string $iteratorClass = "ArrayIterator"
    ) {
        $newArray = [];
        foreach ($array as $block) {
            foreach ($block as $key => $item) {
                $state = new StateDto(
                    $key,
                    isset($item['selected_date']) ? new DateTime($item['selected_date']) : null,
                    $item['type'] ?? null,
                    StateType::tryFrom($item['type'] ?? ''),
                );

                $newArray[$key] = $state;
            }
        }

        parent::__construct($newArray, $flags, $iteratorClass);
    }

    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }
}
