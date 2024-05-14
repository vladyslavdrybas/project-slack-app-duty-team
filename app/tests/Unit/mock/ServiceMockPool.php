<?php
declare(strict_types=1);

namespace App\Tests\Unit\mock;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\ClassDiscriminatorFromClassMetadata;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
use Symfony\Component\Serializer\NameConverter\MetadataAwareNameConverter;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class ServiceMockPool
{
    private static array $instances = [];

    public const SERVICE_SERIALIZER = 'serializer';
    public const SERVICE_REQUEST_DATA = 'requestData';
    public const SERVICE_CONFIG_MOCK = 'configMock';
    protected function __construct() { }

    public static function serializer(): SerializerInterface
    {
        if (!isset(self::$instances[self::SERVICE_SERIALIZER])) {
            $classMetadataFactory = new ClassMetadataFactory(new AttributeLoader());
            $discriminator = new ClassDiscriminatorFromClassMetadata($classMetadataFactory);
            $metadataAwareNameConverter = new MetadataAwareNameConverter($classMetadataFactory);
            $encoders = ['json' => new JsonEncoder()];
            $normalizers = [
                new ObjectNormalizer($classMetadataFactory, $metadataAwareNameConverter, null, null, $discriminator),
                new PropertyNormalizer(),
                new GetSetMethodNormalizer(),
            ];

            self::$instances[self::SERVICE_SERIALIZER] = new Serializer($normalizers, $encoders);
        }

        return self::$instances[self::SERVICE_SERIALIZER];
    }

    public static function requestData(): RequestDataMock
    {
        if (!isset(self::$instances[self::SERVICE_REQUEST_DATA])) {
            self::$instances[self::SERVICE_REQUEST_DATA] = new RequestDataMock();
        }

        return self::$instances[self::SERVICE_REQUEST_DATA];
    }

    public static function config(): ConfigMock
    {
        if (!isset(self::$instances[self::SERVICE_CONFIG_MOCK])) {
            self::$instances[self::SERVICE_CONFIG_MOCK] = new ConfigMock();
        }

        return self::$instances[self::SERVICE_CONFIG_MOCK];
    }
}
