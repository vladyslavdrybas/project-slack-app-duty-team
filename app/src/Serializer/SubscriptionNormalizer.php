<?php

declare(strict_types=1);

namespace App\Serializer;

use App\Entity\Subscription;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class SubscriptionNormalizer extends AbstractEntityNormalizer
{

    /**
     * @param Subscription $object
     * @inheritDoc
     */
    public function normalize(mixed $object, ?string $format = null, array $context = []): array|string|int|float|bool|\ArrayObject|null
    {
        $data = $this->normalizer->normalize(
            $object,
            $format,
            [
                AbstractNormalizer::CALLBACKS => [
                    'subscriber' => [$this, 'shortObject'],
                    'subscriptionPlan' => [$this, 'shortObject'],
                ],
                AbstractNormalizer::IGNORED_ATTRIBUTES => [
                    'rawId',
                    'projects',
                    'payed',
                ],
            ]
        );

        $data['isPayed'] = $object->isPayed();

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof Subscription;
    }

    /**
     * @inheritDoc
     */
    public function getSupportedTypes(?string $format): array
    {
        return [
            Subscription::class => true,
        ];
    }
}
