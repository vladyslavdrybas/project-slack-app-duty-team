<?php
declare(strict_types=1);

namespace App\Entity;

use App\Repository\SlackCommandRepository;
use App\Services\DutyTeamSlackBot\Config\CommandName;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SlackCommandRepository::class, readOnly: false)]
#[ORM\Table(name: "slack_command")]
class SlackCommand extends AbstractEntity
{
    #[ORM\ManyToOne(targetEntity: SlackChannel::class)]
    #[ORM\JoinColumn(name:'channel_id', referencedColumnName: 'id', nullable: false)]
    protected SlackChannel $channel;

    #[ORM\ManyToOne(targetEntity: SlackUser::class)]
    #[ORM\JoinColumn(name:'user_id', referencedColumnName: 'id', nullable: false)]
    protected SlackUser $user;

    #[ORM\Column( name: "text", type: Types::TEXT, nullable: false)]
    protected string $text;

    # month, year
    #[ORM\Column(name: "command_name", type: Types::STRING, length: 237, enumType: CommandName::class)]
    protected CommandName $commandName;

    public function getChannel(): SlackChannel
    {
        return $this->channel;
    }

    public function setChannel(SlackChannel $channel): void
    {
        $this->channel = $channel;
    }

    public function getUser(): SlackUser
    {
        return $this->user;
    }

    public function setUser(SlackUser $user): void
    {
        $this->user = $user;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): void
    {
        $this->text = $text;
    }

    public function getCommandName(): CommandName
    {
        return $this->commandName;
    }

    public function setCommandName(CommandName $commandName): void
    {
        $this->commandName = $commandName;
    }
}
