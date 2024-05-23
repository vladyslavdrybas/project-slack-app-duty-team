<?php
declare(strict_types=1);

namespace App\Services\DutyTeamSlackBot\DataTransferObject\Command;

use App\Services\DutyTeamSlackBot\DataTransferObject\Interactivity\Blocks\ActionCollection;
use App\Services\DutyTeamSlackBot\DataTransferObject\Interactivity\Blocks\StateCollection;

readonly class InteractivityDetailsDto
{
    public function __construct(
       public string $type,
       public StateCollection $states,
       public ActionCollection $actions
    ) {}
}
