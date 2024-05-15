<?php
declare(strict_types=1);

namespace App\Entity;

use App\Repository\SlackCommandRepository;
use App\Services\DutyTeamSlackBot\Config\CommandList;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SlackCommandRepository::class, readOnly: false)]
#[ORM\Table(name: "slack_command")]
class SlackCommand extends AbstractEntity
{
    #[ORM\ManyToOne(targetEntity: SlackChannel::class)]
    #[ORM\JoinColumn(name:'channel_id', referencedColumnName: 'id', nullable: false)]
    protected SlackChannel $channel;

    #[ORM\ManyToOne(targetEntity: SlackTeam::class)]
    #[ORM\JoinColumn(name:'team_id', referencedColumnName: 'id', nullable: false)]
    protected SlackTeam $team;

    #[ORM\ManyToOne(targetEntity: SlackUser::class)]
    #[ORM\JoinColumn(name:'user_id', referencedColumnName: 'id', nullable: false)]
    protected SlackUser $user;

    #[ORM\Column( name: "data", type: Types::JSON)]
    protected array $data;

    # month, year
    #[ORM\Column(name: "command_name", type: Types::STRING, length: 237, enumType: CommandList::class)]
    protected CommandList $commandName;

    public function getChannel(): SlackChannel
    {
        return $this->channel;
    }

    public function setChannel(SlackChannel $channel): void
    {
        $this->channel = $channel;
    }

    public function getTeam(): SlackTeam
    {
        return $this->team;
    }

    public function setTeam(SlackTeam $team): void
    {
        $this->team = $team;
    }

    public function getUser(): SlackUser
    {
        return $this->user;
    }

    public function setUser(SlackUser $user): void
    {
        $this->user = $user;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): void
    {
        $this->data = $data;
    }

    public function getCommandName(): CommandList
    {
        return $this->commandName;
    }

    public function setCommandName(CommandList $commandName): void
    {
        $this->commandName = $commandName;
    }
}
