<?php
declare(strict_types=1);

namespace App\Services\DutyTeamSlackBot\Config;

enum CommandList: string
{
    case SkillsAdd = 'skills-add';
    case SkillsRemove = 'skills-remove';
    case SkillsShow = 'skills-show';

    public function isSkillsCommand(): bool
    {
        return match ($this) {
            self::SkillsAdd, self::SkillsRemove, self::SkillsShow => true,
            default => false,
        };
    }
}
