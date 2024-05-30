<?php
declare(strict_types=1);

namespace App\Services\SlackBot\DataTransferObject;

use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Serializer\Annotation\SerializedPath;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class ViewDto
{
    public function __construct(
        public string $id,

        public ?string $type,

        public ?string $username,

        #[SerializedName('team_id')]
        public ?string $teamId,

        #[SerializedName('app_id')]
        public ?string $appId,

        #[SerializedName('bot_id')]
        public ?string $botId,

        #[SerializedPath('[state][values]')]
        #[Context([
            AbstractNormalizer::CALLBACKS => [
                'state' => [__CLASS__, 'stateToCollection'],
            ]
        ])]
        public null|array|StateCollection $state
    ) {}

    public static function stateToCollection(?array $state = []): StateCollection
    {
        return new StateCollection($state);
    }
}
