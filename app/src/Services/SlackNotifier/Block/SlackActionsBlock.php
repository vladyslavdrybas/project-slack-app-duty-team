<?php
declare(strict_types=1);

namespace App\Services\SlackNotifier\Block;

use Symfony\Component\Notifier\Bridge\Slack\Block\AbstractSlackBlock;

class SlackActionsBlock extends AbstractSlackBlock
{
    public function __construct()
    {
        $this->options['type'] = 'actions';
    }

    /**
     * @return $this
     */
    public function buttonAction(
        string $text,
        string $actionId,
        string $value,
        ?string $style = null
    ): static {
        if (25 === \count($this->options['elements'] ?? [])) {
            throw new \LogicException('Maximum number of elements should not exceed 25.');
        }

        $element = [
            'type' => 'button',
            'text' => [
                'type' => 'plain_text',
                'text' => $text,
            ],
            'action_id' => $actionId,
            'value' => $value,
        ];

        if ($style) {
            // primary or danger
            $element['style'] = $style;
        }

        $this->options['elements'][] = $element;

        return $this;
    }

    /**
     * @return $this
     */
    public function datepicker(
        \DateTimeInterface $initialDate,
        ?string $actionId = null,
        ?string $placeholder = null,
    ): static {
        if (25 === \count($this->options['elements'] ?? [])) {
            throw new \LogicException('Maximum number of elements should not exceed 25.');
        }

        $element = [
            'type' => 'datepicker',
            'initial_date' => $initialDate->format('Y-m-d'),
        ];

        if (null !== $actionId) {
            $element['action_id'] = $actionId;
        }

        if (null !== $placeholder) {
            $element['placeholder'] = [
                'type' => 'plain_text',
                'text' => $placeholder,
            ];
        }

        $this->options['elements'][] = $element;

        return $this;
    }
}
