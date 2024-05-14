<?php
declare(strict_types=1);

namespace App\Tests;

use App\Tests\mock\ConfigMock;
use App\Tests\mock\RequestDataMock;
use App\Tests\mock\ServiceMockPool;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\SerializerInterface;

class UnitTestCase extends TestCase
{
    protected function serializer(): SerializerInterface
    {
        return ServiceMockPool::serializer();
    }

    protected function requestData(): RequestDataMock
    {
        return ServiceMockPool::requestData();
    }

    protected function config(): ConfigMock
    {
        return ServiceMockPool::config();
    }
}
