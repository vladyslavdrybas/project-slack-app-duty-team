<?php
declare(strict_types=1);

namespace App\Services\DutyTeamSlackBot\DataTransferObject\Transformer;

use App\Services\DutyTeamSlackBot\DataTransferObject\ChannelDto;
use App\Services\DutyTeamSlackBot\DataTransferObject\Interactivity\Blocks\ActionCollection;
use App\Services\DutyTeamSlackBot\DataTransferObject\Interactivity\Blocks\StateCollection;
use App\Services\DutyTeamSlackBot\DataTransferObject\Interactivity\ButtonActionElement;
use App\Services\DutyTeamSlackBot\DataTransferObject\Interactivity\DatePickerState;
use App\Services\DutyTeamSlackBot\DataTransferObject\Interactivity\InteractivityDto;
use App\Services\DutyTeamSlackBot\DataTransferObject\Interactivity\SlackInteractivityInputDto;
use App\Services\DutyTeamSlackBot\DataTransferObject\Interactivity\TextInputState;
use App\Services\DutyTeamSlackBot\DataTransferObject\TeamDto;
use App\Services\DutyTeamSlackBot\DataTransferObject\UserDto;
use DateTime;

class SlackInteractivityTransformer
{
    public function transform(SlackInteractivityInputDto $input): InteractivityDto
    {
        $states = new StateCollection();
        $actions = new ActionCollection();

        foreach ($input->states as $blockState) {
            if (is_array($blockState)) {
                foreach ($blockState as $key => $state) {
                    switch ($state['type']) {
                        case 'datepicker':
                            $states->offsetSet(
                                $key,
                                new DatePickerState(new DateTime($state['selected_date']))
                            );
                            break;
                        case 'plain_text_input':
                            $states->offsetSet(
                                $key,
                                new TextInputState($state['value'])
                            );
                            break;
                    }
                }
            }
        }

        foreach ($input->actions as $action) {
            if ('button' === $action['type']) {
                $actions->append(
                    new ButtonActionElement(
                        $action['action_id'],
                        $action['value'],
                        $action['text']['text']
                    )
                );
            }
        }

        return new InteractivityDto(
            $input->token,
            $input->apiAppId,
            $input->triggerId,
            new TeamDto($input->teamId, $input->teamDomain),
            new ChannelDto($input->channelId, $input->channelName),
            new UserDto($input->userId, $input->userName),
            $input->type,
            $states,
            $actions
        );
    }
}
