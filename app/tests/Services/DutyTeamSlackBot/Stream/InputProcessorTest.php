<?php
declare(strict_types=1);

namespace App\Tests\Services\DutyTeamSlackBot\Stream;

use App\Services\DutyTeamSlackBot\Config\CommandList;
use App\Services\DutyTeamSlackBot\DataTransferObject\ChannelDto;
use App\Services\DutyTeamSlackBot\DataTransferObject\Command\CommandDto;
use App\Services\DutyTeamSlackBot\DataTransferObject\Command\SlackCommandInputDto;
use App\Services\DutyTeamSlackBot\DataTransferObject\TeamDto;
use App\Services\DutyTeamSlackBot\DataTransferObject\Transformer\SlackCommandTransformer;
use App\Services\DutyTeamSlackBot\DataTransferObject\UserDto;
use App\Tests\UnitTestCase;

class InputProcessorTest extends UnitTestCase
{
    protected function getCommandDto(): CommandDto
    {
        $addSkillsCommandData = $this->requestData()->getAddSkillsCommand();
        $dto = $this->serializer()->denormalize($addSkillsCommandData, SlackCommandInputDto::class);
        $transformer = new SlackCommandTransformer();

        return $transformer->transform($dto);
    }

    public function testReceiveCommandCheckInputDto(): void
    {
        $dto = $this->getCommandDto();

        $this->assertObjectHasProperty('token', $dto);
        $this->assertObjectHasProperty('team', $dto);
        $this->assertObjectHasProperty('channel', $dto);
        $this->assertObjectHasProperty('user', $dto);
        $this->assertObjectHasProperty('command', $dto);
        $this->assertObjectHasProperty('text', $dto);
        $this->assertObjectHasProperty('apiAppId', $dto);
        $this->assertObjectHasProperty('triggerId', $dto);

        $this->assertEquals($this->config()->get('SLACK_VERIFICATION_TOKEN'), $dto->token);
        $this->assertEquals($this->config()->get('SLACK_APP_ID'), $dto->apiAppId);

        $this->assertInstanceOf(TeamDto::class, $dto->team);
        $this->assertInstanceOf(ChannelDto::class, $dto->channel);
        $this->assertInstanceOf(UserDto::class, $dto->user);
        $this->assertInstanceOf(CommandList::class, $dto->command);
    }

    public function testReceiveCommandDetectCommandType(): void
    {
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    public function testReceiveCommandValidateCommandData(): void
    {
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    public function testReceiveCommandConvertText(): void
    {
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    public function testReceiveCommandFindOrCreateTeam(): void
    {
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    public function testReceiveCommandFindOrCreateChannel(): void
    {
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    public function testReceiveCommandFindOrCreateUser(): void
    {
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    public function testSuccessReceiveMessage(): void
    {
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }
}
