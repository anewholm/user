<?php namespace AcornAssociated\User\Controllers;

use BackendMenu;
use Backend\Classes\Controller;

/**
 * Languages Backend Controller
 */
class Languages extends Controller
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

        BackendMenu::setContext('AcornAssociated.User', 'user', 'languages');
    }
}
