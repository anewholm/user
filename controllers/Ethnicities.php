<?php namespace Acorn\User\Controllers;

use BackendMenu;
use Backend\Classes\Controller;

/**
 * Ethnicities Backend Controller
 */
class Ethnicities extends Controller
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

        BackendMenu::setContext('Acorn.User', 'user', 'ethnicities');
    }
}
