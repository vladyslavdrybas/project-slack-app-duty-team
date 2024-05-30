<?php
declare(strict_types=1);

namespace App\Services\SlackBot\DataTransferObject;

use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

readonly class SlackMessageRequestDto
{
    public function __construct(
        public string $type,
        public string $token,

        #[SerializedName('api_app_id')]
        public ?string $apiAppId,

        public ?string $challenge,

        #[SerializedName('team_id')]
        public ?string $teamId,

        #[SerializedName('event_id')]
        public ?string           $eventId,

        public ?TeamDto          $team,
        public ?UserDto          $user,
        public ?EventDto         $event,
        public ?ViewDto          $view,
        public ?ContainerDto     $container,

        #[Context([
            AbstractNormalizer::CALLBACKS => [
                'actions' => [__CLASS__, 'actionsToCollection'],
            ]
        ])]
        public null|array|ActionCollection $actions,
    ) { }

    public static function actionsToCollection(?array $actions = []): ActionCollection
    {
        return new ActionCollection($actions);
    }
}
