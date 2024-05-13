<?php
declare(strict_types=1);

namespace App\Tests\mock;

use App\Services\DutyTeamSlackBot\Config\CommandList;
use Symfony\Component\Finder\Finder;

class RequestDataMock
{
    protected readonly string $slackDataPath;

    public function __construct()
    {
        $this->slackDataPath = dirname(__DIR__) . '/data/slack';
    }

    public function getAddSkillsCommand(): array
    {
        $finder = new Finder();
        $finder->in($this->slackDataPath)->files()->name('command*skills*add.json')->contains('/' . CommandList::SkillsAdd->value);

        if ($finder->hasResults()) {
            foreach ($finder as $file) {
                return json_decode($file->getContents(), true);
            }
        }

        return [];
    }
}
