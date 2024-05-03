<?php

declare(strict_types=1);

namespace App\Serializer;

use App\Entity\EntityInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use function array_pop;
use function explode;

class EntityDenormalizer implements DenormalizerInterface
{
    public function __construct(
        protected readonly EntityManagerInterface $em
    ) {}

    public function supportsDenormalization($data, $type, $format = null, array $context = []): bool
    {
        return (str_starts_with($type, 'App\\Entity\\')) &&
            (is_numeric($data) || is_string($data) || (is_array($data) && isset($data['id'])));
    }

    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): mixed
    {
        $entity = $this->em->find($type, $data);

        if (null === $entity) {
            throw new InvalidArgumentException(
                sprintf(
                    'Input value exception. %s with id %s not found.',
                    $this->cleanTypeNamespace($type),
                    $data
                )
            );
        }

        return $entity;
    }

    protected function cleanTypeNamespace(string $type): string
    {
        $namespace = explode('\\', $type);

        return array_pop($namespace);
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            EntityInterface::class => true,
        ];
    }
}
