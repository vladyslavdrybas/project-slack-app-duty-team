<?php
declare(strict_types=1);

namespace App\Tests\Unit\Services\SlackBot\DataTransferObject;

use App\Services\SlackBot\DataTransferObject\ActionCollection;
use App\Services\SlackBot\DataTransferObject\EventDto;
use App\Services\SlackBot\DataTransferObject\SlackMessageRequestDto;
use App\Services\SlackBot\DataTransferObject\StateCollection;
use App\Tests\UnitTestCase;

class SlackMessageRequestDtoTest extends UnitTestCase
{
    public function testMessageIsUrlVerification(): void
    {
        $request = $this->requestData()->getUrlVerificationMessage();

        $request = $this->serializer()->serialize($request, 'json');
        $dto = $this->serializer()->deserialize($request, SlackMessageRequestDto::class, 'json');

        $this->assertObjectHasProperty('type', $dto);
        $this->assertObjectHasProperty('token', $dto);
        $this->assertObjectHasProperty('challenge', $dto);
    }

    public function testMessageIsHomeTabOpened(): void
    {
        $request = $this->requestData()->getEventOpenedHomeTabMessage();

        $request = $this->serializer()->serialize($request, 'json');
        $dto = $this->serializer()->deserialize($request, SlackMessageRequestDto::class, 'json');

        $this->assertObjectHasProperty('type', $dto);
        $this->assertObjectHasProperty('token', $dto);
        $this->assertObjectHasProperty('apiAppId', $dto);
        $this->assertObjectHasProperty('teamId', $dto);
        $this->assertObjectHasProperty('eventId', $dto);
        $this->assertObjectHasProperty('event', $dto);

        $this->assertInstanceOf(EventDto::class, $dto->event);
    }

    public function testMessageIsMessagesTabOpened(): void
    {
        $request = $this->requestData()->getEventOpenedMessagesTabMessage();

        $request = $this->serializer()->serialize($request, 'json');
        $dto = $this->serializer()->deserialize($request, SlackMessageRequestDto::class, 'json');

        $this->assertObjectHasProperty('type', $dto);
        $this->assertObjectHasProperty('token', $dto);
        $this->assertObjectHasProperty('apiAppId', $dto);
        $this->assertObjectHasProperty('teamId', $dto);
        $this->assertObjectHasProperty('eventId', $dto);
        $this->assertObjectHasProperty('event', $dto);

        $this->assertInstanceOf(EventDto::class, $dto->event);
    }

    public function testMessageIsHomeButtonClick(): void
    {
        $request = $this->requestData()->getHomeButtonClickMessage();

        $request = $this->serializer()->serialize($request, 'json');
        $dto = $this->serializer()->deserialize(
            $request,
            SlackMessageRequestDto::class,
            'json',
        );


        $this->assertObjectHasProperty('type', $dto);
        $this->assertObjectHasProperty('token', $dto);
        $this->assertObjectHasProperty('apiAppId', $dto);
        $this->assertObjectHasProperty('team', $dto);
        $this->assertObjectHasProperty('user', $dto);
        $this->assertObjectHasProperty('view', $dto);
        $this->assertObjectHasProperty('container', $dto);
        $this->assertObjectHasProperty('actions', $dto);

        $this->assertInstanceOf(ActionCollection::class, $dto->actions);
        $this->assertInstanceOf(StateCollection::class, $dto->view->state);

        $this->assertTrue(!$dto->actions->isEmpty());
        $this->assertTrue(!$dto->view->state->isEmpty());
    }
}
