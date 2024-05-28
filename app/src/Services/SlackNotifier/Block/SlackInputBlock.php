<?php
declare(strict_types=1);

namespace App\Services\SlackNotifier\Block;

use Symfony\Component\Notifier\Bridge\Slack\Block\AbstractSlackBlock;
use Symfony\Component\Notifier\Bridge\Slack\Block\SlackBlockElementInterface;

final class SlackInputBlock extends AbstractSlackBlock
{
    public function __construct()
    {
        $this->options['type'] = 'input';
        $this->options['label'] = [
            'type' => 'plain_text',
            'text' => '',
        ];
    }

    public function element(SlackBlockElementInterface $element): self
    {
        $this->options['element'] = $element->toArray();

        return $this;
    }

    public function label(string $text): self
    {
        $this->options['label'] = [
            'type' => 'plain_text',
            'text' => $text,
        ];

        return $this;
    }

}
