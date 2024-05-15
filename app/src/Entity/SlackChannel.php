<?php
declare(strict_types=1);

namespace App\Entity;

use App\Repository\SlackChannelRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SlackChannelRepository::class, readOnly: false)]
#[ORM\Table(name: "slack_channel")]
class SlackChannel extends AbstractEntity
{
    #[ORM\Column( name: "channel_id", type: Types::STRING, length: 180, unique: true, nullable: false)]
    protected string $channelId;

    #[ORM\Column( name: "channel_name", type: Types::STRING, length: 180, unique: false, nullable: false)]
    protected string $channelName;

    public function getChannelId(): string
    {
        return $this->channelId;
    }

    public function setChannelId(string $channelId): void
    {
        $this->channelId = $channelId;
    }

    public function getChannelName(): string
    {
        return $this->channelName;
    }

    public function setChannelName(string $channelName): void
    {
        $this->channelName = $channelName;
    }
}
