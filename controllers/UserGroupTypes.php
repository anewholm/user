<?php namespace Acorn\User\Controllers;

use BackendMenu;
use Backend\Classes\Controller;

/**
 * User Group Types Backend Controller
 */
class UserGroupTypes extends Controller
{
    /**
     * @var array Behaviors that are implemented by this controller.
     */
    public $implement = [
        \Backend\Behaviors\FormController::class,
        \Backend\Behaviors\ListController::class,
    ];

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Acorn.User', 'user', 'usergrouptypes');
    }
}
