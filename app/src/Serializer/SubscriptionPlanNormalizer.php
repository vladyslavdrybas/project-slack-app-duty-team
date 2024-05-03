<?php

declare(strict_types=1);

namespace App\Serializer;

use App\Entity\SubscriptionPlan;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class SubscriptionPlanNormalizer extends AbstractEntityNormalizer
{

    /**
     * @param SubscriptionPlan $object
     * @inheritDoc
     */
    public function normalize(mixed $object, ?string $format = null, array $context = []): array|string|int|float|bool|\ArrayObject|null
    {
        $data = $this->normalizer->normalize(
            $object,
            $format,
            [
                AbstractNormalizer::IGNORED_ATTRIBUTES => [
                    'rawId',
                ],
            ]
        );

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof SubscriptionPlan;
    }

    /**
     * @inheritDoc
     */
    public function getSupportedTypes(?string $format): array
    {
        return [
            SubscriptionPlan::class => true,
        ];
    }
}
