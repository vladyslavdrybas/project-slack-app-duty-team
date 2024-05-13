<?php
declare(strict_types=1);

namespace App\Tests\Services\DutyTeamSlackBot\Stream;

use App\Tests\mock\RequestDataMock;
use PHPUnit\Framework\TestCase;

class InputProcessorTest extends TestCase
{
    public function testReceiveCommand(): void
    {
        $requestDataMock = new RequestDataMock();

        $addSkillsCommandData = $requestDataMock->getAddSkillsCommand();

        dump($addSkillsCommandData);
    }

    public function testReceiveMessage(): void
    {
    }
}
