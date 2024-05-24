<?php
declare(strict_types=1);

namespace App\Services\SlackNotifier\Block;

use Symfony\Component\Notifier\Bridge\Slack\Block\AbstractSlackBlockElement;

final class SlackTextInputBlockElement extends AbstractSlackBlockElement
{
    public function __construct(
        ?string $actionId = null
    ) {
        $this->options = [
            'type' => 'plain_text_input',
            'multiline' => true,
            'action_id' => $actionId
        ];
    }
}
