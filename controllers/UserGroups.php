<?php namespace AcornAssociated\User\Controllers;

use Flash;
use BackendMenu;
use AcornAssociated\Collection;
use AcornAssociated\Controller;
use AcornAssociated\User\Models\UserGroup;

/**
 * User Groups Back-end Controller
 */
class UserGroups extends Controller
{
    use \AcornAssociated\Traits\PathsHelper;

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
    public $requiredPermissions = ['acornassociated.users.access_groups'];

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('AcornAssociated.User', 'user', 'usergroups');
    }
}
