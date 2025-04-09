<?php namespace AcornAssociated\User;

use App;
use Auth;
use Event;
use Backend;
use Backend\Models\User as BackendUser;
use Backend\Controllers\Users as BackendUsers;
use AcornAssociated\User\Models\User;
use AcornAssociated\User\Models\UserGroup;
use System\Classes\PluginBase;
use System\Classes\SettingsManager;
use Illuminate\Foundation\AliasLoader;
use AcornAssociated\User\Classes\UserRedirector;
use AcornAssociated\User\Models\MailBlocker;
use AcornAssociated\User\Console\SetDefaults;
use AcornAssociated\User\Console\CreateUser;
use \AcornAssociated\Events\ModelBeforeSave;
use \AcornAssociated\User\Listeners\CompleteCreatedByUser;
use Winter\Notify\Classes\Notifier;

class Plugin extends PluginBase
{
    /**
     * @var boolean Determine if this plugin should have elevated privileges.
     */
    public $elevated = true;

    // AcornAssociated.User CANNOT depend on anything
    // because everything depends on it
    // through replicateable created_by_user_id
    public $require = [];

    public function pluginDetails()
    {
        return [
            'name'        => 'acornassociated.user::lang.plugin.name',
            'description' => 'acornassociated.user::lang.plugin.description',
            'author'      => 'Acorn Associated',
            'icon'        => 'icon-address-book',
        ];
    }

    public function boot()
    {
        Event::listen('backend.page.beforeDisplay', function ($controller, $action, $params) {
            $controller->addCss('~/plugins/acornassociated/user/assets/css/plugin.css');
        });

        Event::listen('backend.menu.extendItems', function ($navigationManager) {
            if ($user = User::authUser()) {
                $userGroups = $user->groups;
                $menu       = array();
                $userGroups->each(function($userGroup) use (&$menu, &$userGroups) {
                    $userGroupName = (strlen($userGroup->name) < 20 ? $userGroup->name : substr($userGroup->name,0,20) . '...');
                    if (!$menu) $menu = array(
                        'current_auth_groups' => array(
                            'label' => $userGroupName,
                            'icon' => 'icon-group',
                            // 'counter' => ($userGroups->count() > 1 ? $userGroups->count() : NULL),
                            'url' => $userGroup->controllerUrl('update', $userGroup->id()),
                            'sideMenu' => array(),
                        ),
                    );
                    else {
                        $menu['current_auth_groups']['sideMenu'][$userGroupName] = array(
                            'label' => $userGroupName,
                            'icon' => 'icon-group',
                            'url' => $userGroup->controllerUrl('update', $userGroup->id()),
                        );
                    };
                });
                $navigationManager->addMainMenuItems('acornassociated_user', $menu);
            }
        });

        BackendUser::extend(function ($model){
            $model->belongsTo['user'] = [User::class, 'key' => 'acornassociated_user_user_id'];
        });

        BackendUsers::extendFormFields(function ($form, $model, $context) {
            if ($model instanceof BackendUser) {
                $userGroups = array();
                $model->load('user');
                if ($model->user && $model->user->groups)
                    $userGroups = $model->user->groups->pluck('name')->toArray();

                // TODO: Permissions: can_change_own_user, can_change_others_user
                $form->addTabFields([
                    'acornassociated_user_section' => [
                        'label'   => 'acornassociated.user::lang.backend.acornassociated_user_section',
                        'type'    => 'section',
                        'comment' => 'acornassociated.user::lang.backend.acornassociated_user_section_comment',
                        'commentHtml' => TRUE,
                        'tab'     => 'acornassociated.user::lang.plugin.name',
                    ],
                    'user' => [
                        'label'   => 'acornassociated.user::lang.backend.acornassociated_user',
                        'type'    => 'dropdown',
                        'span'    => 'auto',
                        'placeholder' => 'backend::lang.form.select',
                        'options' => '\AcornAssociated\User\Models\User::dropdownOptions',
                        'comment' => 'acornassociated.user::lang.backend.acornassociated_user_comment',
                        'commentHtml' => TRUE,
                        'tab'     => 'acornassociated.user::lang.plugin.name',
                    ],
                    '_acornassociated_user_groups' => [
                        'label'   => 'acornassociated.user::lang.backend.acornassociated_user_groups',
                        'type'    => 'checkboxlist',
                        'options' => $userGroups,
                        'span'    => 'auto',
                        'cssClass' => 'nolabel',
                        // TODO: Remove checkboxes and disable control
                        // TODO: Change the groups list when user changes
                        // 'dependsOn' => 'user', 
                        'comment' => 'acornassociated.user::lang.backend.acornassociated_user_groups_comment',
                        'commentHtml' => TRUE,
                        'tab'     => 'acornassociated.user::lang.plugin.name',
                    ],
                ]);
            }
        });

        // Fill out created_by_user_id fields
        Event::listen(
            ModelBeforeSave::class,
            [CompleteCreatedByUser::class, 'handle']
        );

        Event::listen('backend.list.injectRowClass', function ($listWidget, $record, &$value) {
            if ($record instanceof UserGroup) {
                if ($record->isAuthUserGroup()) {
                    $value .= 'usergroup-current-auth';
                }
            }
        });
    }

    public function register()
    {
        $alias = AliasLoader::getInstance();
        $alias->alias('Auth', 'AcornAssociated\User\Facades\Auth');

        $this->registerConsoleCommand('user.set-defaults', SetDefaults::class);
        $this->registerConsoleCommand('user.create-user', CreateUser::class);

        App::singleton('user.auth', function () {
            return \AcornAssociated\User\Classes\AuthManager::instance();
        });

        App::singleton('redirect', function ($app) {
            // overrides with our own extended version of Redirector to support
            // seperate url.intended session variable for frontend
            $redirector = new UserRedirector($app['url']);

            // If the session is set on the application instance, we'll inject it into
            // the redirector instance. This allows the redirect responses to allow
            // for the quite convenient "with" methods that flash to the session.
            if (isset($app['session.store'])) {
                $redirector->setSession($app['session.store']);
            }

            return $redirector;
        });

        /*
         * Apply user-based mail blocking
         */
        Event::listen('mailer.prepareSend', function ($mailer, $view, $message) {
            return MailBlocker::filterMessage($view, $message);
        });

        /*
         * Compatability with Winter.Notify
         */
        $this->bindNotificationEvents();
    }

    public function registerComponents()
    {
        return [
            \AcornAssociated\User\Components\Session::class       => 'session',
            \AcornAssociated\User\Components\Account::class       => 'account',
            \AcornAssociated\User\Components\ResetPassword::class => 'resetPassword'
        ];
    }

    public function registerPermissions()
    {
        return [
            'acornassociated.users.manage_front_end' => [
                'tab'   => 'acornassociated.user::lang.plugin.tab',
                'label' => 'acornassociated.user::lang.plugin.manage_front_end'
            ],
            'acornassociated.users.access_users' => [
                'tab'   => 'acornassociated.user::lang.plugin.tab',
                'label' => 'acornassociated.user::lang.plugin.access_users'
            ],
            'acornassociated.users.access_groups' => [
                'tab'   => 'acornassociated.user::lang.plugin.tab',
                'label' => 'acornassociated.user::lang.plugin.access_groups'
            ],
            'acornassociated.users.access_settings' => [
                'tab'   => 'acornassociated.user::lang.plugin.tab',
                'label' => 'acornassociated.user::lang.plugin.access_settings'
            ],
            'acornassociated.users.impersonate_user' => [
                'tab'   => 'acornassociated.user::lang.plugin.tab',
                'label' => 'acornassociated.user::lang.plugin.impersonate_user'
            ],
        ];
    }

    public function registerNavigation()
    {
        return [
            'user' => [
                'label'       => 'acornassociated.user::lang.plugin.menu_label',
                'url'         => Backend::url('acornassociated/user/users'),
                'icon'        => 'icon-address-book',
                'permissions' => ['acornassociated.users.*'],
                'order'       => 500,

                'sideMenu' => [
                    'users' => [
                        'label' => 'acornassociated.user::lang.users.menu_label',
                        'icon'        => 'icon-user',
                        'url'         => Backend::url('acornassociated/user/users'),
                        'permissions' => ['acornassociated.users.access_users']
                    ],
                    'usergroups' => [
                        'label'       => 'acornassociated.user::lang.groups.menu_label',
                        'icon'        => 'icon-users-viewfinder',
                        'url'         => Backend::url('acornassociated/user/usergroups'),
                        'permissions' => ['acornassociated.users.access_groups']
                    ],
                    'usergrouptypes' => [
                        'label'       => 'acornassociated.user::lang.models.usergrouptype.label_plural',
                        'icon'        => 'icon-stripe',
                        'url'         => Backend::url('acornassociated/user/usergrouptypes'),
                        'permissions' => ['acornassociated.users.access_groups']
                    ],
                    'languages' => [
                        'label'       => 'acornassociated.user::lang.models.language.label_plural',
                        'icon'        => 'icon-wechat',
                        'url'         => Backend::url('acornassociated/user/languages'),
                        'permissions' => ['acornassociated.users.access_languages']
                    ]
                ]
            ]
        ];
    }

    public function registerSettings()
    {
        return [
            'settings' => [
                'label'       => 'acornassociated.user::lang.settings.menu_label',
                'description' => 'acornassociated.user::lang.settings.menu_description',
                'category'    => 'AcornAssociated',
                'icon'        => 'icon-user-gear',
                'class'       => 'AcornAssociated\User\Models\Settings',
                'order'       => 500,
                'permissions' => ['acornassociated.users.access_settings']
            ]
        ];
    }

    public function registerMailTemplates()
    {
        return [
            'acornassociated.user::mail.activate',
            'acornassociated.user::mail.welcome',
            'acornassociated.user::mail.restore',
            'acornassociated.user::mail.new_user',
            'acornassociated.user::mail.reactivate',
            'acornassociated.user::mail.invite',
        ];
    }

    public function registerNotificationRules()
    {
        if (!class_exists(\Winter\Notify\Classes\Notifier::class)) {
            return [];
        }

        return [
            'groups' => [
                'user' => [
                    'label' => 'User',
                    'icon' => 'icon-user'
                ],
            ],
            'events' => [
               \AcornAssociated\User\NotifyRules\UserActivatedEvent::class,
               \AcornAssociated\User\NotifyRules\UserRegisteredEvent::class,
            ],
            'actions' => [],
            'conditions' => [
                \AcornAssociated\User\NotifyRules\UserAttributeCondition::class,
            ],
        ];
    }

    protected function bindNotificationEvents()
    {
        if (!class_exists(\Winter\Notify\Classes\Notifier::class)) {
            return;
        }

        Notifier::bindEvents([
            'acornassociated.user.activate' => \AcornAssociated\User\NotifyRules\UserActivatedEvent::class,
            'acornassociated.user.register' => \AcornAssociated\User\NotifyRules\UserRegisteredEvent::class,
        ]);

        Notifier::instance()->registerCallback(function ($manager) {
            $manager->registerGlobalParams([
                'user' => Auth::getUser()
            ]);
        });
    }
}
