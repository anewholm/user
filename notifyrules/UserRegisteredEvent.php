<?php namespace Acorn\User\NotifyRules;

use Acorn\User\Classes\UserEventBase;

class UserRegisteredEvent extends UserEventBase
{
    /**
     * Returns information about this event, including name and description.
     */
    public function eventDetails()
    {
        return [
            'name'        => 'Registered',
            'description' => 'A user has registered',
            'group'       => 'user'
        ];
    }
}
