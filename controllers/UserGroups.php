<?php namespace Acorn\User\Controllers;

use Flash;
use BackendMenu;
use Acorn\Collection;
use Acorn\Controller;
use Acorn\User\Models\UserGroup;

/**
 * User Groups Back-end Controller
 */
class UserGroups extends Controller
{
    use \Acorn\Traits\PathsHelper;

    /**
     * @var array Extensions implemented by this controller.
     */
    public $implement = [
        \Backend\Behaviors\FormController::class,
        \Backend\Behaviors\ListController::class
    ];

    /**
     * @var array `FormController` configuration.
     */
    public $formConfig = 'config_form.yaml';

    /**
     * @var array `ListController` configuration.
     */
    public $listConfig = 'config_list.yaml';

    /**
     * @var array `RelationController` configuration, by extension.
     */
    public $relationConfig;

    /**
     * @var array Permissions required to view this page.
     */
    public $requiredPermissions = ['acorn.users.access_groups'];

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Acorn.User', 'user', 'usergroups');
    }
}
