<?php
declare(strict_types=1);

namespace App\Services\DutyTeamSlackBot\Config;

enum CommandList: string
{
    case SkillsAdd = 'skills-add';
    case SkillsRemove = 'skills-remove';
    case SkillsShow = 'skills-show';
    case TimeOff = 'timeoff';
    case TimeOffBtnAdd = 'timeoff-btn-add';
    case TimeOffShow = 'timeoff-show';

    public function isSkillsCommand(): bool
    {
        return match ($this) {
            self::SkillsAdd, self::SkillsRemove, self::SkillsShow => true,
            default => false,
        };
    }

    public function isTimeOffCommand(): bool
    {
        return match ($this) {
            self::TimeOff, self::TimeOffBtnAdd, self::TimeOffShow => true,
            default => false,
        };
    }
}
