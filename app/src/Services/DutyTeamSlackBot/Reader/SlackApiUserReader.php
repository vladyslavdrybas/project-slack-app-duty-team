<?php
declare(strict_types=1);

namespace App\Services\DutyTeamSlackBot\Reader;

use App\Services\DutyTeamSlackBot\DataTransferObject\ISlackMessageIdentifier;
use App\Services\DutyTeamSlackBot\DataTransferObject\reader\UserInfoDto;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SlackApiUserReader
{
    public function __construct(
        readonly protected HttpClientInterface $slackClient,
        readonly protected SerializerInterface $serializer,
        protected readonly LoggerInterface $slackInputLogger
    ) {}

    public function read(ISlackMessageIdentifier $dto): ?UserInfoDto
    {
        $response = $this->slackClient->request('GET', '/api/users.info', [
            'query' => [
                'user' => $dto->getUser()->userId,
            ],
        ]);
        $content = $response->getContent();
        $this->slackInputLogger->debug('slack user info response', [$content]);


        /** @var UserInfoDto $userInfo */
        $userInfo = $this->serializer->deserialize($content, UserInfoDto::class, 'json');
        $this->slackInputLogger->debug('slack user info response dto', [$userInfo]);

        if (false === $userInfo->isSuccess) {
            return null;
        }

        return $userInfo;
    }
}
