<?php namespace Acorn\User;

use App;
use Auth;
use Event;
use Backend;
use Backend\Models\User as BackendUser;
use Acorn\User\Models\User;
use System\Classes\PluginBase;
use System\Classes\SettingsManager;
use Illuminate\Foundation\AliasLoader;
use Acorn\User\Classes\UserRedirector;
use Acorn\User\Models\MailBlocker;
use \Acorn\Events\ModelBeforeSave;
use \Acorn\User\Listeners\CompleteCreatedByUser;
use Winter\Notify\Classes\Notifier;

class Plugin extends PluginBase
{
    /**
     * @var boolean Determine if this plugin should have elevated privileges.
     */
    public $elevated = true;

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
        BackendUser::extend(function ($model){
            $model->belongsTo['user'] = [User::class, 'key' => 'acorn_user_user_id'];
        });

        // Fill out created_by_user_id fields
        Event::listen(
            ModelBeforeSave::class,
            [CompleteCreatedByUser::class, 'handle']
        );
    }

    public function register()
    {
        $alias = AliasLoader::getInstance();
        $alias->alias('Auth', 'Acorn\User\Facades\Auth');

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
