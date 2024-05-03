<?php

declare(strict_types=1);

namespace App\Serializer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Uid\UuidV7;

class Uuid7Normalizer implements NormalizerInterface
{
    /**
     * @param UuidV7 $object
     * @param string|null $format
     * @param array $context
     * @return string
     */
    public function normalize($object, string $format = null, array $context = []): string
    {
        return $object->toRfc4122();
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof UuidV7;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            UuidV7::class => true,
        ];
    }
}
