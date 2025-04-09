<?php namespace AcornAssociated\User\Models;

use Model;

class Settings extends Model
{
    /**
     * @var array Behaviors implemented by this model.
     */
    public $implement = [
        \System\Behaviors\SettingsModel::class
    ];

    public $settingsCode = 'user_settings';
    public $settingsFields = 'fields.yaml';


    const ACTIVATE_AUTO = 'auto';
    const ACTIVATE_USER = 'user';
    const ACTIVATE_ADMIN = 'admin';

    const LOGIN_EMAIL = 'email';
    const LOGIN_USERNAME = 'username';

    const REMEMBER_ALWAYS = 'always';
    const REMEMBER_NEVER = 'never';
    const REMEMBER_ASK = 'ask';

    public function initSettingsData()
    {
        $this->require_activation = config('acornassociated.user::requireActivation', true);
        $this->activate_mode = config('acornassociated.user::activateMode', self::ACTIVATE_AUTO);
        $this->use_throttle = config('acornassociated.user::useThrottle', true);
        $this->block_persistence = config('acornassociated.user::blockPersistence', false);
        $this->allow_registration = config('acornassociated.user::allowRegistration', true);
        $this->login_attribute = config('acornassociated.user::loginAttribute', self::LOGIN_EMAIL);
        $this->remember_login = config('acornassociated.user::rememberLogin', self::REMEMBER_ALWAYS);
        $this->use_register_throttle = config('acornassociated.user::useRegisterThrottle', true);
    }

    public function getActivateModeOptions()
    {
        return [
            self::ACTIVATE_AUTO => [
                'acornassociated.user::lang.settings.activate_mode_auto',
                'acornassociated.user::lang.settings.activate_mode_auto_comment'
            ],
            self::ACTIVATE_USER => [
                'acornassociated.user::lang.settings.activate_mode_user',
                'acornassociated.user::lang.settings.activate_mode_user_comment'
            ],
            self::ACTIVATE_ADMIN => [
                'acornassociated.user::lang.settings.activate_mode_admin',
                'acornassociated.user::lang.settings.activate_mode_admin_comment'
            ]
        ];
    }

    public function getActivateModeAttribute($value)
    {
        if (!$value) {
            return self::ACTIVATE_AUTO;
        }

        return $value;
    }

    public function getLoginAttributeOptions()
    {
        return [
            self::LOGIN_EMAIL => ['acornassociated.user::lang.login.attribute_email'],
            self::LOGIN_USERNAME => ['acornassociated.user::lang.login.attribute_username']
        ];
    }

    public function getRememberLoginOptions()
    {
        return [
            self::REMEMBER_ALWAYS => [
                'acornassociated.user::lang.settings.remember_always',
            ],
            self::REMEMBER_NEVER => [
                'acornassociated.user::lang.settings.remember_never',
            ],
            self::REMEMBER_ASK => [
                'acornassociated.user::lang.settings.remember_ask',
            ]
        ];
    }

    public function getRememberLoginAttribute($value)
    {
        if (!$value) {
            return self::REMEMBER_ALWAYS;
        }

        return $value;
    }
}
