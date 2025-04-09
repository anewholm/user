<?php namespace AcornAssociated\User\Facades;

use Winter\Storm\Support\Facade;

/**
 * @see \AcornAssociated\User\Classes\AuthManager
 */
class Auth extends Facade
{
    /**
     * Get the registered name of the component.
     * @return string
     */
    protected static function getFacadeAccessor() { return 'user.auth'; }
}
