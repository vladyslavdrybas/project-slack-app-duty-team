<?php
declare(strict_types=1);

namespace App\Tests\Unit\mock;

use App\Services\DutyTeamSlackBot\Config\CommandName;
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
        return $this->getData($this->slackDataPath, 'command_skills_add.json', CommandName::Skills->value);
    }

    public function getAddSkillsInteractivityMessage(): array
    {
        return $this->getData($this->slackDataPath, 'interactivity_payload_skills_add.json', CommandName::SkillsBtnAdd->value);
    }

    public function getInteractivityMessage(): array
    {
        return $this->getData($this->slackDataPath, 'interactivity_payload_timeoff_add.json', 'not-exist');
    }

    public function getUrlVerificationMessage(): array
    {
        return $this->getData($this->slackDataPath, 'url_verification.json', 'url_verification');
    }

    public function getEventOpenedHomeTabMessage(): array
    {
        return $this->getData($this->slackDataPath, 'event_app_home_opened_home.json', 'event_app_home_opened_home');
    }

    public function getEventOpenedMessagesTabMessage(): array
    {
        return $this->getData($this->slackDataPath, 'event_app_home_opened_messages.json', 'event_app_home_opened_messages');
    }

    public function getHomeButtonClickMessage(): array
    {
        return $this->getData($this->slackDataPath, 'home_button_skills_show_response.json', 'home_button_skills_show_response');
    }
}
