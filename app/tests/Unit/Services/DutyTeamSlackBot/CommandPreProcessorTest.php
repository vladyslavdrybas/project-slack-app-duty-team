<?php
declare(strict_types=1);

namespace App\Tests\Unit\Services\DutyTeamSlackBot;

use App\Builder\UserBuilder;
use App\Entity\SlackChannel;
use App\Entity\SlackTeam;
use App\Entity\SlackUser;
use App\Repository\SlackChannelRepository;
use App\Repository\SlackTeamRepository;
use App\Repository\SlackUserRepository;
use App\Services\DutyTeamSlackBot\CommandPreProcessor;
use App\Services\DutyTeamSlackBot\Config\CommandName;
use App\Services\DutyTeamSlackBot\DataTransferObject\Command\CommandDto;
use App\Services\DutyTeamSlackBot\DataTransferObject\Command\SlackCommandInputDto;
use App\Services\DutyTeamSlackBot\DataTransferObject\Transformer\SlackCommandTransformer;
use App\Services\DutyTeamSlackBot\Reader\SlackApiUserReader;
use App\Tests\UnitTestCase;
use Doctrine\ORM\EntityManagerInterface;

class CommandPreProcessorTest extends UnitTestCase
{
    public function testReceiveUnknownCommand(): void
    {
        $slackDto = new SlackCommandInputDto(
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '/not-exist-cmd',
            '',
            '',
            ''
        );
        $transformer = new SlackCommandTransformer();
        $dto = $transformer->transform($slackDto);

        $this->assertObjectHasProperty('command', $dto);
        $this->assertEquals(null, $dto->command);
    }

    public function testProcessCommandThrowInvalidTokenException(): void
    {
        $this->expectExceptionMessage('Invalid bot token.');

        $commandProcessor = $this->mockCommandProcessor();
        $slackDto = new SlackCommandInputDto(
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            ''
        );
        $transformer = new SlackCommandTransformer();
        $dto = $transformer->transform($slackDto);

        $commandProcessor->process($dto);
    }

    public function testProcessCommandThrowAppApiException(): void
    {
        $this->expectExceptionMessage('Invalid app id.');

        $commandProcessor = $this->mockCommandProcessor();

        $slackDto = new SlackCommandInputDto(
            $this->mockParameterBag()->get('duty_team_slack_bot_verification_token'),
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            ''
        );
        $transformer = new SlackCommandTransformer();
        $dto = $transformer->transform($slackDto);

        $commandProcessor->process($dto);
    }

    public function testProcessCommandThrowInvalidCommandException(): void
    {
        $this->expectExceptionMessage('Invalid command.');

        $commandProcessor = $this->mockCommandProcessor();

        $slackDto = new SlackCommandInputDto(
            $this->mockParameterBag()->get('duty_team_slack_bot_verification_token'),
            '',
            '',
            '',
            '',
            '',
            '',
            '/not-exist-cmd',
            '',
            $this->mockParameterBag()->get('duty_team_slack_bot_app_id'),
            ''
        );
        $transformer = new SlackCommandTransformer();
        $dto = $transformer->transform($slackDto);

        $commandProcessor->process($dto);
    }

    public function testReceiveCommandProcessor(): void
    {
        $commandDto = $this->getAddSkillsCommandDto();

        $slackUser = new SlackUser();
        $slackUser->setUserId($commandDto->user->userId);
        $slackUser->setUserName($commandDto->user->userName);

        $slackChannel = new SlackChannel();
        $slackChannel->setChannelId($commandDto->channel->channelId);
        $slackChannel->setChannelName($commandDto->channel->channelName);

        $slackUserRepository = $this->createMock(SlackUserRepository::class);
        $slackUserRepository->expects($this->atLeastOnce())
            ->method('findOneBy')
            ->willReturn($slackUser);

        $slackChannelRepository = $this->createMock(SlackChannelRepository::class);
        $slackChannelRepository->expects($this->atLeastOnce())
            ->method('findOneBy')
            ->willReturn($slackChannel);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->atLeastOnce())
            ->method('getRepository')
            ->willReturnMap([
                [SlackUser::class, $slackUserRepository],
                [SlackChannel::class, $slackChannelRepository],
            ]);

        $commandProcessor = new CommandPreProcessor(
            $this->mockParameterBag(),
            $entityManager,
            $this->logger(),
            $this->createMock(SlackApiUserReader::class),
            $this->createMock(UserBuilder::class)
        );

        $slackCommand = $commandProcessor->process($commandDto);

        $this->assertEquals($commandDto->text, $slackCommand->getText());
        $this->assertEquals($slackUser, $slackCommand->getUser());
        $this->assertEquals($slackChannel, $slackCommand->getChannel());
        $this->assertEquals(CommandName::Skills, $slackCommand->getCommandName());
    }

    protected function getAddSkillsCommandDto(): CommandDto
    {
        $commandDto = $this->requestData()->getAddSkillsCommand();
        $dto = $this->serializer()->denormalize($commandDto, SlackCommandInputDto::class);
        $transformer = new SlackCommandTransformer();

        return $transformer->transform($dto);
    }

    protected function mockCommandProcessor(): CommandPreProcessor
    {
        return new CommandPreProcessor(
            $this->mockParameterBag(),
            $this->createMock(EntityManagerInterface::class),
            $this->logger(),
            $this->createMock(SlackApiUserReader::class),
            $this->createMock(UserBuilder::class)
        );
    }
}
