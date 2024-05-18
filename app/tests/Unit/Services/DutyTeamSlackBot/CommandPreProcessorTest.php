<?php
declare(strict_types=1);

namespace App\Tests\Unit\Services\DutyTeamSlackBot;

use App\Entity\SlackChannel;
use App\Entity\SlackTeam;
use App\Entity\SlackUser;
use App\Repository\SlackChannelRepository;
use App\Repository\SlackTeamRepository;
use App\Repository\SlackUserRepository;
use App\Services\DutyTeamSlackBot\CommandPreProcessor;
use App\Services\DutyTeamSlackBot\Config\CommandList;
use App\Services\DutyTeamSlackBot\DataTransferObject\ChannelDto;
use App\Services\DutyTeamSlackBot\DataTransferObject\Command\CommandDto;
use App\Services\DutyTeamSlackBot\DataTransferObject\Command\SlackCommandInputDto;
use App\Services\DutyTeamSlackBot\DataTransferObject\TeamDto;
use App\Services\DutyTeamSlackBot\DataTransferObject\Transformer\SlackCommandTransformer;
use App\Services\DutyTeamSlackBot\DataTransferObject\UserDto;
use App\Tests\UnitTestCase;
use Doctrine\ORM\EntityManagerInterface;

class CommandPreProcessorTest extends UnitTestCase
{
    public function testReceiveCommandCheckInputDto(): void
    {
        $dto = $this->getAddSkillsCommandDto();

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

        $slackTeam = new SlackTeam();
        $slackTeam->setTeamId($commandDto->team->teamId);
        $slackTeam->setTeamDomain($commandDto->team->teamDomain);

        $slackChannel = new SlackChannel();
        $slackChannel->setChannelId($commandDto->channel->channelId);
        $slackChannel->setChannelName($commandDto->channel->channelName);

        $slackUserRepository = $this->createMock(SlackUserRepository::class);
        $slackUserRepository->expects($this->atLeastOnce())
            ->method('findOneBy')
            ->willReturn($slackUser);

        $slackTeamRepository = $this->createMock(SlackTeamRepository::class);
        $slackTeamRepository->expects($this->atLeastOnce())
            ->method('findOneBy')
            ->willReturn($slackTeam);

        $slackChannelRepository = $this->createMock(SlackChannelRepository::class);
        $slackChannelRepository->expects($this->atLeastOnce())
            ->method('findOneBy')
            ->willReturn($slackChannel);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->atLeastOnce())
            ->method('getRepository')
            ->willReturnMap([
                [SlackUser::class, $slackUserRepository],
                [SlackTeam::class, $slackTeamRepository],
                [SlackChannel::class, $slackChannelRepository],
            ]);

        $commandProcessor = new CommandPreProcessor(
            $this->mockParameterBag(),
            $entityManager,
            $this->logger()
        );

        $slackCommand = $commandProcessor->process($commandDto);

        $this->assertEquals(['php','javascript','docker','redis'], $slackCommand->getData());
        $this->assertEquals($slackUser, $slackCommand->getUser());
        $this->assertEquals($slackTeam, $slackCommand->getTeam());
        $this->assertEquals($slackChannel, $slackCommand->getChannel());
        $this->assertEquals(CommandList::SkillsAdd, $slackCommand->getCommandName());
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
            $this->logger()
        );
    }
}
