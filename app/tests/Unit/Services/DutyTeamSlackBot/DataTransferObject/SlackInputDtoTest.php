<?php
declare(strict_types=1);

namespace App\Tests\Unit\Services\DutyTeamSlackBot\DataTransferObject;

use App\Services\DutyTeamSlackBot\Config\CommandName;
use App\Services\DutyTeamSlackBot\DataTransferObject\ChannelDto;
use App\Services\DutyTeamSlackBot\DataTransferObject\Command\CommandDto;
use App\Services\DutyTeamSlackBot\DataTransferObject\Command\SlackCommandInputDto;
use App\Services\DutyTeamSlackBot\DataTransferObject\Interactivity\Blocks\ActionCollection;
use App\Services\DutyTeamSlackBot\DataTransferObject\Interactivity\Blocks\StateCollection;
use App\Services\DutyTeamSlackBot\DataTransferObject\Interactivity\IActionElement;
use App\Services\DutyTeamSlackBot\DataTransferObject\Interactivity\InteractivityDto;
use App\Services\DutyTeamSlackBot\DataTransferObject\Interactivity\SlackInteractivityInputDto;
use App\Services\DutyTeamSlackBot\DataTransferObject\ISlackMessageIdentifier;
use App\Services\DutyTeamSlackBot\DataTransferObject\TeamDto;
use App\Services\DutyTeamSlackBot\DataTransferObject\Transformer\SlackCommandTransformer;
use App\Services\DutyTeamSlackBot\DataTransferObject\Transformer\SlackInteractivityTransformer;
use App\Services\DutyTeamSlackBot\DataTransferObject\UserDto;
use App\Tests\UnitTestCase;

class SlackInputDtoTest extends UnitTestCase
{
    public function testReceiveAddSkillsInteractivityInputDto(): void
    {
        $dto = $this->getAddSkillsInteractivityDto();

        $this->assertObjectHasProperty('token', $dto);
        $this->assertObjectHasProperty('apiAppId', $dto);
        $this->assertObjectHasProperty('triggerId', $dto);
        $this->assertObjectHasProperty('team', $dto);
        $this->assertObjectHasProperty('channel', $dto);
        $this->assertObjectHasProperty('user', $dto);
        $this->assertObjectHasProperty('type', $dto);
        $this->assertObjectHasProperty('states', $dto);
        $this->assertObjectHasProperty('actions', $dto);

        $this->assertInstanceOf(ISlackMessageIdentifier::class, $dto);
        $this->assertInstanceOf(TeamDto::class, $dto->getTeam());
        $this->assertInstanceOf(ChannelDto::class, $dto->getChannel());
        $this->assertInstanceOf(UserDto::class, $dto->getUser());
        $this->assertInstanceOf(StateCollection::class, $dto->getStates());
        $this->assertInstanceOf(ActionCollection::class, $dto->getActions());
    }

    public function testReceiveInteractivityInputDto(): void
    {
        $dto = $this->getAddTimeOffInteractivityDto();

        $this->assertObjectHasProperty('token', $dto);
        $this->assertObjectHasProperty('apiAppId', $dto);
        $this->assertObjectHasProperty('triggerId', $dto);
        $this->assertObjectHasProperty('team', $dto);
        $this->assertObjectHasProperty('channel', $dto);
        $this->assertObjectHasProperty('user', $dto);
        $this->assertObjectHasProperty('type', $dto);
        $this->assertObjectHasProperty('states', $dto);
        $this->assertObjectHasProperty('actions', $dto);

        $this->assertInstanceOf(ISlackMessageIdentifier::class, $dto);
        $this->assertInstanceOf(TeamDto::class, $dto->getTeam());
        $this->assertInstanceOf(ChannelDto::class, $dto->getChannel());
        $this->assertInstanceOf(UserDto::class, $dto->getUser());
        $this->assertInstanceOf(StateCollection::class, $dto->getStates());
        $this->assertInstanceOf(ActionCollection::class, $dto->getActions());

        $this->assertEquals(2, $dto->states->count());
        $this->assertEquals(1, $dto->actions->count());
        $this->assertInstanceOf(IActionElement::class, $dto->getActions()->getIterator()->current());
    }

    protected function getAddSkillsInteractivityDto(): InteractivityDto
    {
        $dto = $this->requestData()->getAddSkillsInteractivityMessage();
        $dto = $this->serializer()->denormalize($dto, SlackInteractivityInputDto::class);
        $transformer = new SlackInteractivityTransformer();

        return $transformer->transform($dto);
    }

    protected function getAddTimeOffInteractivityDto(): InteractivityDto
    {
        $dto = $this->requestData()->getInteractivityMessage();
        $dto = $this->serializer()->denormalize($dto, SlackInteractivityInputDto::class);
        $transformer = new SlackInteractivityTransformer();

        return $transformer->transform($dto);
    }
}
