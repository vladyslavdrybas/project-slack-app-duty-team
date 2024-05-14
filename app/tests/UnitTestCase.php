<?php
declare(strict_types=1);

namespace App\Tests;

use App\Tests\Unit\mock\ConfigMock;
use App\Tests\Unit\mock\RequestDataMock;
use App\Tests\Unit\mock\ServiceMockPool;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
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

    protected function mockParameterBag(): ParameterBagInterface
    {
        $mock = $this->createMock(ParameterBagInterface::class);
        $mock->expects($this->any())
            ->method('get')
            ->withAnyParameters()
            ->willReturnCallback(function (string $key) {
                return match ($key) {
                    'duty_team_slack_bot_verification_token' => $this->config()->get('SLACK_VERIFICATION_TOKEN'),
                    'duty_team_slack_bot_app_id' => $this->config()->get('SLACK_APP_ID'),
                };
            })
        ;

        return $mock;
    }
}
