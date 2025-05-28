<?php namespace Acorn\User\Controllers;

use Flash;
use BackendMenu;
use Acorn\Collection;
use Acorn\Controller;
use Acorn\User\Models\UserGroup;

/**
 * User Group Versions Back-end Controller
 */
class UserGroupVersions extends Controller
{
    use \Acorn\Traits\PathsHelper;

    /**
     * @var array Extensions implemented by this controller.
     */
    public $implement = [
        \Backend\Behaviors\FormController::class,
        \Backend\Behaviors\ListController::class,
        \Backend\Behaviors\RelationController::class
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
    public $requiredPermissions = ['acorn.users.access_group_versions'];

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Acorn.User', 'user', 'usergroupversions');
    }
}
