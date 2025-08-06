<?php namespace Acorn\User\Controllers;

use BackendMenu;
use Acorn\Controller;
use Acorn\User\Models\UserGroup;
use Acorn\User\Models\User;

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

    public $belongsTo = [
        'user_group' => [UserGroup::class, 'table' => 'acorn_user_user_groups'],
    ];

    public $hasMany = [
        'users' => [User::class, 'table' => 'acorn_user_user_group_version'],
    ];

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Acorn.User', 'user', 'usergroupversions');
    }
}
