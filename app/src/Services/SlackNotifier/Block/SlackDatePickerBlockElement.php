<?php
declare(strict_types=1);

namespace App\Services\SlackNotifier\Block;

use Symfony\Component\Notifier\Bridge\Slack\Block\AbstractSlackBlockElement;

final class SlackDatePickerBlockElement extends AbstractSlackBlockElement
{
    public function __construct(
        \DateTimeInterface $initialDate,
        ?string $actionId = null,
        ?string $placeholder = null
    ) {
        $this->options = [
            'type' => 'datepicker',
            'action_id' => $actionId,
            'initial_date' => $initialDate->format('Y-m-d'),
            'placeholder' => [
                'type' => 'plain_text',
                'text' => $placeholder,
            ]
        ];
    }
}
