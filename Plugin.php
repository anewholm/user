<?php namespace Acorn\User;

use App;
use Auth;
use Event;
use Backend;
use Backend\Models\User as BackendUser;
use Backend\Controllers\Users as BackendUsers;
use Acorn\User\Models\User;
use Acorn\User\Models\UserGroup;
use System\Classes\PluginBase;
use System\Classes\SettingsManager;
use Illuminate\Foundation\AliasLoader;
use Acorn\User\Classes\UserRedirector;
use Acorn\User\Models\MailBlocker;
use Acorn\User\Console\SetDefaults;
use Acorn\User\Console\CreateUser;
use \Acorn\Events\ModelBeforeSave;
use Winter\Notify\Classes\Notifier;

class Plugin extends PluginBase
{
    /**
     * @var boolean Determine if this plugin should have elevated privileges.
     */
    public $elevated = true;

    // Acorn.User CANNOT depend on anything
    // because everything depends on it
    // through replicateable created_by_user_id
    public $require = [];

    public function pluginDetails()
    {
        return [
            'name'        => 'acorn.user::lang.plugin.name',
            'description' => 'acorn.user::lang.plugin.description',
            'author'      => 'Acorn',
            'icon'        => 'icon-address-book',
        ];
    }

    public function boot()
    {
        Event::listen('backend.page.beforeDisplay', function ($controller, $action, $params) {
            $controller->addCss('~/plugins/acorn/user/assets/css/plugin.css');
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
                            'url' => $userGroup->controllerUrl('update', $userGroup->id),
                            'sideMenu' => array(),
                        ),
                    );
                    else {
                        $menu['current_auth_groups']['sideMenu'][$userGroupName] = array(
                            'label' => $userGroupName,
                            'icon' => 'icon-group',
                            'url' => $userGroup->controllerUrl('update', $userGroup->id),
                        );
                    };
                });
                $navigationManager->addMainMenuItems('acorn_user', $menu);
            }
        });

        BackendUser::extend(function ($model){
            $model->belongsTo['user'] = [User::class, 'key' => 'acorn_user_user_id'];
        });

        BackendUsers::extendListColumns(function ($list, $user) {
            if ($user instanceof BackendUser) {
                $list->addColumns([
                    'user' => [
                        'label'    => 'acorn.user::lang.backend.acorn_user_section',
                        'type'     => 'text',
                        'relation' => 'user',
                        'select'   => 'name',
                        'attributes' => array('autocomplete' => 'off'),
                    ],
                    'acorn_create_and_sync_aa_user' => [
                        'label'      => 'acorn.user::lang.backend.acorn_create_and_sync_aa_user',
                        'type'       => 'switch',
                        'invisible'  => true,
                        'attributes' => array('autocomplete' => 'off'),
                    ],
                ]);
            }
        });

        BackendUsers::extendFormFields(function ($form, $model, $context) {
            if ($model instanceof BackendUser) {
                $model->bindEvent('model.beforeValidate', function () use(&$model) {
                    if ($model->email == '') $model->email = NULL;
                });

                // ------------------------ Sync backend_user => User
                $model->bindEvent('model.beforeSave', function () use(&$model) {
                    if ($model->acorn_create_and_sync_aa_user) {
                        if (!$model->user) {
                            $model->user = User::create([
                                'name'     => ($model->first_name ?: $model->login),
                                'surname'  => $model->last_name,
                                'email'    => $model->email,
                                // Initially cannot login on the front end
                                // TODO: Inherit backend_user passwords
                                'username' => $model->login,
                                'password' => 'password',
                                'password_confirmation' => 'password',
                                'is_activated' => FALSE,
                            ]);
                        } else {
                            // Sync
                            // TODO: Sync password?
                            $model->user->name     = $model->first_name;
                            $model->user->surname  = $model->last_name;
                            $model->user->email    = $model->email;
                            $model->user->username = $model->login;
                            $model->user->save();
                        }
                    }
                });

                // ---------------------------- User fields
                $form->addTabFields([
                    'acorn_user_section' => [
                        'label'   => 'acorn.user::lang.backend.acorn_user_section',
                        'type'    => 'section',
                        'comment' => 'acorn.user::lang.backend.acorn_user_section_comment',
                        'commentHtml' => TRUE,
                        'tab'     => 'acorn.user::lang.plugin.name',
                        'permissions' => array('acorn.user.change_backend_user'),
                    ],
                    'acorn_create_and_sync_aa_user' => [
                        'label'    => 'acorn.user::lang.backend.acorn_create_and_sync_aa_user',
                        'type'     => 'switch',
                        'default'  => true,
                        'span'     => 'left',
                        'comment'  => 'acorn.user::lang.backend.acorn_create_and_sync_aa_user_comment',
                        'commentHtml' => TRUE,
                        'attributes'  => array('autocomplete' => 'off'),
                        'tab'      => 'acorn.user::lang.plugin.name',
                        'permissions' => array('acorn.user.change_create_and_sync_aa_user'),
                    ],
                    'user' => [
                        // TODO: This backend user relation field just won't work
                        'label'    => 'acorn.user::lang.backend.acorn_user',
                        'type'     => 'text',
                        'span'     => 'right',
                        'placeholder' => 'backend::lang.form.select',
                        'options'  => '\Acorn\User\Models\User::dropdownOptions',
                        'comment'  => "acorn.user::lang.backend.acorn_user_comment",
                        'commentHtml' => TRUE,
                        'disabled' => TRUE,
                        'context'  => 'update',
                        'permissions' => array('acorn.user.change_backend_user'),
                        'tab'      => 'acorn.user::lang.plugin.name',
                    ],
                ]);
            }
        });

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
        $alias->alias('Auth', 'Acorn\User\Facades\Auth');

        $this->registerConsoleCommand('user.set-defaults', SetDefaults::class);
        $this->registerConsoleCommand('user.create-user', CreateUser::class);

        App::singleton('user.auth', function () {
            return \Acorn\User\Classes\AuthManager::instance();
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
            \Acorn\User\Components\Session::class       => 'session',
            \Acorn\User\Components\Account::class       => 'account',
            \Acorn\User\Components\ResetPassword::class => 'resetPassword'
        ];
    }

    public function registerPermissions()
    {
        return [
            'acorn.users.manage_front_end' => [
                'tab'   => 'acorn.user::lang.plugin.tab',
                'label' => 'acorn.user::lang.plugin.manage_front_end'
            ],
            'acorn.users.access_users' => [
                'tab'   => 'acorn.user::lang.plugin.tab',
                'label' => 'acorn.user::lang.plugin.access_users'
            ],
            'acorn.users.access_groups' => [
                'tab'   => 'acorn.user::lang.plugin.tab',
                'label' => 'acorn.user::lang.plugin.access_groups'
            ],
            'acorn.users.access_settings' => [
                'tab'   => 'acorn.user::lang.plugin.tab',
                'label' => 'acorn.user::lang.plugin.access_settings'
            ],
            'acorn.users.impersonate_user' => [
                'tab'   => 'acorn.user::lang.plugin.tab',
                'label' => 'acorn.user::lang.plugin.impersonate_user'
            ],
            'acorn.user.change_backend_user' => [
                'tab'   => 'acorn.user::lang.plugin.tab',
                'label' => 'acorn.user::lang.backend.acorn_user_section'
            ],
            // TODO: Move these to permissions below
            'acorn.user.change_create_and_sync_aa_user' => [
                'tab'   => 'acorn.user::lang.plugin.tab',
                'label' => 'acorn.user::lang.backend.acorn_create_and_sync_aa_user'
            ],

            // New field permissions
            // Referenced by create-system
            'acorn.user.user_fathers_name_view' => [
                'tab'   => 'acorn.user::lang.plugin.tab',
                'label' => 'acorn.user::lang.permissions.user_fathers_name_view'

            ],
            'acorn.user.user_fathers_name_change' => [
                'tab'   => 'acorn.user::lang.plugin.tab',
                'label' => 'acorn.user::lang.permissions.user_fathers_name_change'

            ],
            'acorn.user.user_surname_view' => [
                'tab'   => 'acorn.user::lang.plugin.tab',
                'label' => 'acorn.user::lang.permissions.user_surname_view'
            ],
            'acorn.user.user_surname_change' => [
                'tab'   => 'acorn.user::lang.plugin.tab',
                'label' => 'acorn.user::lang.permissions.user_surname_change'
            ],
            'acorn.user.user_mothers_name_view' => [
                'tab'   => 'acorn.user::lang.plugin.tab',
                'label' => 'acorn.user::lang.permissions.user_mothers_name_view'
            ],
            'acorn.user.user_mothers_name_change' => [
                'tab'   => 'acorn.user::lang.plugin.tab',
                'label' => 'acorn.user::lang.permissions.user_mothers_name_change'
            ],
            'acorn.user.user_gender_view' => [
                'tab'   => 'acorn.user::lang.plugin.tab',
                'label' => 'acorn.user::lang.permissions.user_gender_view'
            ],
            'acorn.user.user_gender_change' => [
                'tab'   => 'acorn.user::lang.plugin.tab',
                'label' => 'acorn.user::lang.permissions.user_gender_change'
            ],
            'acorn.user.user_marital_status_view' => [
                'tab'   => 'acorn.user::lang.plugin.tab',
                'label' => 'acorn.user::lang.permissions.user_marital_status_view'
            ],
            'acorn.user.user_marital_status_change' => [
                'tab'   => 'acorn.user::lang.plugin.tab',
                'label' => 'acorn.user::lang.permissions.user_marital_status_change'
            ],
            'acorn.user.user_email_view' => [
                'tab'   => 'acorn.user::lang.plugin.tab',
                'label' => 'acorn.user::lang.permissions.user_email_view'
            ],
            'acorn.user.user_email_change' => [
                'tab'   => 'acorn.user::lang.plugin.tab',
                'label' => 'acorn.user::lang.permissions.user_email_change'
            ],
            'acorn.user.user_send_invite_view' => [
                'tab'   => 'acorn.user::lang.plugin.tab',
                'label' => 'acorn.user::lang.permissions.user_send_invite_view'
            ],
            'acorn.user.user_send_invite_change' => [
                'tab'   => 'acorn.user::lang.plugin.tab',
                'label' => 'acorn.user::lang.permissions.user_send_invite_change'
            ],
            'acorn.user.user_password_view' => [
                'tab'   => 'acorn.user::lang.plugin.tab',
                'label' => 'acorn.user::lang.permissions.user_password_view'
            ],
            'acorn.user.user_password_change' => [
                'tab'   => 'acorn.user::lang.plugin.tab',
                'label' => 'acorn.user::lang.permissions.user_password_change'
            ],
            'acorn.user.user_password_confirmation_view' => [
                'tab'   => 'acorn.user::lang.plugin.tab',
                'label' => 'acorn.user::lang.permissions.user_password_confirmation_view'
            ],
            'acorn.user.user_password_confirmation_change' => [
                'tab'   => 'acorn.user::lang.plugin.tab',
                'label' => 'acorn.user::lang.permissions.user_password_confirmation_change'
            ],
            'acorn.user.user_username_view' => [
                'tab'   => 'acorn.user::lang.plugin.tab',
                'label' => 'acorn.user::lang.permissions.user_username_view'
            ],
            'acorn.user.user_username_change' => [
                'tab'   => 'acorn.user::lang.plugin.tab',
                'label' => 'acorn.user::lang.permissions.user_username_change'
            ],
            'acorn.user.user_created_ip_address_view' => [
                'tab'   => 'acorn.user::lang.plugin.tab',
                'label' => 'acorn.user::lang.permissions.user_created_ip_address_view'
            ],
            'acorn.user.user_created_ip_address_change' => [
                'tab'   => 'acorn.user::lang.plugin.tab',
                'label' => 'acorn.user::lang.permissions.user_created_ip_address_change'
            ],
            'acorn.user.user_last_ip_address_view' => [
                'tab'   => 'acorn.user::lang.plugin.tab',
                'label' => 'acorn.user::lang.permissions.user_last_ip_address_view'
            ],
            'acorn.user.user_last_ip_address_change' => [
                'tab'   => 'acorn.user::lang.plugin.tab',
                'label' => 'acorn.user::lang.permissions.user_last_ip_address_change'
            ],
            'acorn.user.user_avatar_view' => [
                'tab'   => 'acorn.user::lang.plugin.tab',
                'label' => 'acorn.user::lang.permissions.user_avatar_view'
            ],
            'acorn.user.user_avatar_change' => [
                'tab'   => 'acorn.user::lang.plugin.tab',
                'label' => 'acorn.user::lang.permissions.user_avatar_change'
            ],
            'acorn.user.user_birth_date_view' => [
                'tab'   => 'acorn.user::lang.plugin.tab',
                'label' => 'acorn.user::lang.permissions.user_birth_date_view'
            ],
            'acorn.user.user_birth_date_change' => [
                'tab'   => 'acorn.user::lang.plugin.tab',
                'label' => 'acorn.user::lang.permissions.user_birth_date_change'
            ],
            'acorn.user.user_religion_view' => [
                'tab'   => 'acorn.user::lang.plugin.tab',
                'label' => 'acorn.user::lang.permissions.user_religion_view'
            ],
            'acorn.user.user_religion_change' => [
                'tab'   => 'acorn.user::lang.plugin.tab',
                'label' => 'acorn.user::lang.permissions.user_religion_change'
            ],
            'acorn.user.user_ethnicity_view' => [
                'tab'   => 'acorn.user::lang.plugin.tab',
                'label' => 'acorn.user::lang.permissions.user_ethnicity_view'
            ],
            'acorn.user.user_ethnicity_change' => [
                'tab'   => 'acorn.user::lang.plugin.tab',
                'label' => 'acorn.user::lang.permissions.user_ethnicity_change'
            ],

            # User Group
            'acorn.user.usergroup_code_view' => [
                'tab'   => 'acorn.user::lang.plugin.tab',
                'label' => 'acorn.user::lang.permissions.usergroup_code_view'
            ],
            'acorn.user.usergroup_code_change' => [
                'tab'   => 'acorn.user::lang.plugin.tab',
                'label' => 'acorn.user::lang.permissions.usergroup_code_change'
            ],
            'acorn.user.usergroup_colour_view' => [
                'tab'   => 'acorn.user::lang.plugin.tab',
                'label' => 'acorn.user::lang.permissions.usergroup_colour_view'
            ],
            'acorn.user.usergroup_colour_change' => [
                'tab'   => 'acorn.user::lang.plugin.tab',
                'label' => 'acorn.user::lang.permissions.usergroup_colour_change'
            ],
            'acorn.user.usergroup_image_view' => [
                'tab'   => 'acorn.user::lang.plugin.tab',
                'label' => 'acorn.user::lang.permissions.usergroup_image_view'
            ],
            'acorn.user.usergroup_image_change' => [
                'tab'   => 'acorn.user::lang.plugin.tab',
                'label' => 'acorn.user::lang.permissions.usergroup_image_change'
            ],
        ];
    }

    public function registerNavigation()
    {
        return [
            'user' => [
                'label'       => 'acorn.user::lang.plugin.menu_label',
                'url'         => Backend::url('acorn/user/users'),
                'icon'        => 'icon-address-book',
                'permissions' => ['acorn.users.*'],
                'order'       => 500,

                'sideMenu' => [
                    'users' => [
                        'label' => 'acorn.user::lang.users.menu_label',
                        'icon'        => 'icon-user',
                        'url'         => Backend::url('acorn/user/users'),
                        'permissions' => ['acorn.users.access_users']
                    ],
                    'usergroups' => [
                        'label'       => 'acorn.user::lang.groups.menu_label',
                        'icon'        => 'icon-users-viewfinder',
                        'url'         => Backend::url('acorn/user/usergroups'),
                        'permissions' => ['acorn.users.access_groups']
                    ],
                    'usergroupversions' => [
                        'label'       => 'acorn.user::lang.models.usergroupversions.label_plural',
                        'icon'        => 'icon-users-viewfinder',
                        'url'         => Backend::url('acorn/user/usergroupversions'),
                        'permissions' => ['acorn.users.access_groups']
                    ],
                    'usergrouptypes' => [
                        'label'       => 'acorn.user::lang.models.usergrouptype.label_plural',
                        'icon'        => 'icon-stripe',
                        'url'         => Backend::url('acorn/user/usergrouptypes'),
                        'permissions' => ['acorn.users.access_groups']
                    ],
                    'languages' => [
                        'label'       => 'acorn.user::lang.models.language.label_plural',
                        'icon'        => 'icon-wechat',
                        'url'         => Backend::url('acorn/user/languages'),
                        'permissions' => ['acorn.users.access_languages']
                    ]
                ]
            ]
        ];
    }

    public function registerSettings()
    {
        return [
            'settings' => [
                'label'       => 'acorn.user::lang.settings.menu_label',
                'description' => 'acorn.user::lang.settings.menu_description',
                'category'    => 'Acorn',
                'icon'        => 'icon-user-gear',
                'class'       => 'Acorn\User\Models\Settings',
                'order'       => 500,
                'permissions' => ['acorn.users.access_settings']
            ]
        ];
    }

    public function registerMailTemplates()
    {
        return [
            'acorn.user::mail.activate',
            'acorn.user::mail.welcome',
            'acorn.user::mail.restore',
            'acorn.user::mail.new_user',
            'acorn.user::mail.reactivate',
            'acorn.user::mail.invite',
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
               \Acorn\User\NotifyRules\UserActivatedEvent::class,
               \Acorn\User\NotifyRules\UserRegisteredEvent::class,
            ],
            'actions' => [],
            'conditions' => [
                \Acorn\User\NotifyRules\UserAttributeCondition::class,
            ],
        ];
    }

    protected function bindNotificationEvents()
    {
        if (!class_exists(\Winter\Notify\Classes\Notifier::class)) {
            return;
        }

        Notifier::bindEvents([
            'acorn.user.activate' => \Acorn\User\NotifyRules\UserActivatedEvent::class,
            'acorn.user.register' => \Acorn\User\NotifyRules\UserRegisteredEvent::class,
        ]);

        Notifier::instance()->registerCallback(function ($manager) {
            $manager->registerGlobalParams([
                'user' => Auth::getUser()
            ]);
        });
    }
}
