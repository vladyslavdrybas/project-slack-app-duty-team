<?php
declare(strict_types=1);

namespace App\Services\SlackNotifier\Block;

use Symfony\Component\Notifier\Bridge\Slack\Block\AbstractSlackBlockElement;

final class SlackTextWithActionButtonBlockElement extends AbstractSlackBlockElement
{
    public function __construct(
        string $text,
        string $actionId,
        string $value,
        string $style = 'primary'
    ) {
        $this->options = [
            'type' => 'button',
            'text' => [
                'type' => 'plain_text',
                'text' => $text,
            ],
            'action_id' => $actionId,
            'value' => $value,
            'style' => $style,
        ];
    }
}
