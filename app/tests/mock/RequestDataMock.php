<?php
declare(strict_types=1);

namespace App\Tests\mock;

use App\Services\DutyTeamSlackBot\Config\CommandList;
use Symfony\Component\Finder\Finder;

class RequestDataMock
{
    protected readonly string $slackDataPath;
    protected array $data = [];

    public function __construct()
    {
        $this->slackDataPath = dirname(__DIR__) . '/data/slack';
    }

    protected function getData(string $folder, string $fileName, ?string $alias = null): array
    {
        if (null === $alias) {
            $alias = $fileName;
        }

        if (isset($this->data[$alias])) {
            return $this->data[$alias];
        }

        $finder = new Finder();
        $finder->in($folder)->files()->name($fileName);

        if ($finder->hasResults()) {
            foreach ($finder as $file) {
                $this->data[$alias] = json_decode($file->getContents(), true);

                return $this->data[$alias];
            }
        }

        return [];
    }

    public function getAddSkillsCommand(): array
    {
        return $this->getData($this->slackDataPath, 'command*skills*add.json', CommandList::SkillsAdd->value);
    }
}
