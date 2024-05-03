<?php

declare(strict_types=1);

namespace App\Serializer;

use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

abstract class AbstractEntityNormalizer implements NormalizerInterface
{
    public function __construct(
        protected readonly GetSetMethodNormalizer $normalizer,
    ) {
    }

    public function shortObject(object $innerObject): array
    {
        /** @var \App\Entity\EntityInterface $innerObject */
        return [
            'id' => $innerObject->getRawId(),
            'object' => $innerObject->getObject(),
        ];
    }

    public function normalizeUserInList(object $innerObject): array
    {
        /** @var \App\Entity\User $innerObject */
        return [
            'id' => $innerObject->getRawId(),
            'username' => $innerObject->getUsername(),
            'firstname' => $innerObject->getFirstname(),
            'lastname' => $innerObject->getLastname(),
            'isActive' => $innerObject->isActive(),
        ];
    }
}
