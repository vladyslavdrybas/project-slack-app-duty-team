<?php
declare(strict_types=1);

namespace App\Services\DutyTeamSlackBot\Config;

enum CommandName: string
{
    case Skills = 'skills';
    case SkillsBtnAdd = 'skills-btn-add';
    case SkillsBtnShow = 'skills-btn-show';
    case SkillsBtnRemove = 'skills-btn-remove';
    case TimeOff = 'timeoff';
    case TimeOffBtnAdd = 'timeoff-btn-add';
    case TimeOffBtnShow = 'timeoff-btn-show';
    case TimeOffBtnRemove = 'timeoff-btn-remove';

    public function isSkillsCommand(): bool
    {
        return match ($this) {
            self::Skills, self::SkillsBtnAdd, self::SkillsBtnRemove, self::SkillsBtnShow => true,
            default => false,
        };
    }

    public function isTimeOffCommand(): bool
    {
        return match ($this) {
            self::TimeOff, self::TimeOffBtnAdd, self::TimeOffBtnShow, self::TimeOffBtnRemove => true,
            default => false,
        };
    }
}
