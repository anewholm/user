<?php namespace Acorn\User\Controllers;

use BackendMenu;
use Acorn\Controller;

/**
 * Roles Backend Controller
 */
class Roles extends Controller
{
    /**
     * @var array Behaviors that are implemented by this controller.
     */
    public $implement = [
        \Acorn\Behaviors\FormController::class,
        \Acorn\Behaviors\ListController::class,
    ];

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Acorn.User', 'user', 'roles');
    }
}
