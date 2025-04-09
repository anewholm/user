<?php namespace AcornAssociated\User\Controllers;

use BackendMenu;
use AcornAssociated\Controller;

/**
 * Roles Backend Controller
 */
class Roles extends Controller
{
    /**
     * @var array Behaviors that are implemented by this controller.
     */
    public $implement = [
        \AcornAssociated\Behaviors\FormController::class,
        \AcornAssociated\Behaviors\ListController::class,
    ];

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('AcornAssociated.User', 'user', 'roles');
    }
}
