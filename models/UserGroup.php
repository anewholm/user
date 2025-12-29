<?php namespace Acorn\User\Models;

use Winter\Storm\Auth\Models\Group as GroupBase;
use Str;
use Acorn\Collection;
use Acorn\Model;
use Winter\Storm\Database\TreeCollection;

/**
 * User Group Model
 */
class UserGroup extends GroupBase
{
    use \Illuminate\Database\Eloquent\Concerns\HasUuids;
    use \Acorn\Traits\PathsHelper;
    use \Acorn\Traits\ImplementReplaces;
    use \Winter\Storm\Database\Traits\NestedTree;
    use \Acorn\Backendlocalization\Class\TranslateBackend;
    use \Acorn\Traits\Dropdowns;
    use \Staudenmeir\EloquentHasManyDeep\HasTableAlias;

    const PARENT_ID = 'parent_user_group_id';

    const GROUP_GUEST = 'guest';
    const GROUP_REGISTERED = 'registered';

    // Supporting 1-1 translations saving and loading
    public $implement = ['Acorn.Behaviors.TranslatableModel'];
    // We still throw a fake Winter.Translate.Behaviors in there 
    // as AA model does
    // so that this class still shows the translateable fields
    public $implementReplaces = ['Winter.Translate.Behaviors.TranslatableModel'];

    public $translatable = ['name', 'description'];

    /**
     * @var string The database table used by the model.
     */
    protected $table = 'acorn_user_user_groups';

    /**
     * Validation rules
     */
    public $rules = [
        'name' => 'required|between:3,64',
    ];

    /**
     * @var array Relations
     */
    public $hasMany = [
        'children' => [UserGroup::class, 'key' => 'parent_user_group_id'],
        'versions' => UserGroupVersion::class,
    ];
    public $belongsToMany = [
        'users'       => [User::class, 'table' => 'acorn_user_user_group'],
        'users_count' => [User::class, 'table' => 'acorn_user_user_group', 'count' => true]
    ];
    public $belongsTo = [
        'parent_user_group' => [UserGroup::class, 'key' => 'parent_user_group_id'], 
        'type' => UserGroupType::class,
    ];

    /* This is programatically added by TranslateableModel, 
     * but we have it also here to hint to create-system 
    public $morphMany = [
        'translations' => [
            'Winter\Translate\Models\Attribute',
            'name' => 'model'
        ],
    ];
    */

    /**
     * @var array The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'code',
        'description'
    ];

    protected static $guestGroup = null;

    /**
     * Returns the guest user group.
     * @return Acorn\User\Models\UserGroup
     */
    public static function getGuestGroup()
    {
        if (self::$guestGroup !== null) {
            return self::$guestGroup;
        }

        $group = self::where('code', self::GROUP_GUEST)->first() ?: false;

        return self::$guestGroup = $group;
    }

    // --------------------------------------------- New functions
    // TODO: Place these in a Trait
    public function getChildren()
    {
        return $this->children;
    }

    public function getChildCount(): int
    {
        return $this->children->count();
    }

    public function getNameAttribute()
    {
        $name = $this->attributes['name'];
        $code = $this->attributes['code'];
        if ($code) {
            $code = Str::limit($code, 8);
            $name = "$name ($code)";
        }
        return $name;
    }

    public function setNameAttribute($name)
    {
        $code = $this->attributes['code'];
        $codeString = " ($code)";
        $this->attributes['name'] = str_replace($codeString, '', $name);
    }

    public static function authUserGroups(): TreeCollection|NULL
    {
        $usergroups = NULL;
        if ($user = User::authUser()) {
            $user->load('groups');
            $usergroups = $user->groups;
        }
        return $usergroups;
    }

    public static function authUserGroupsOptions(): array
    {
        return self::authUserGroups()->pluck('name','id')->toArray();
    }

    public function isAuthUserGroup(): bool
    {
        return (self::authUserGroups()->contains($this));
    }

    protected function getAuthIsMemberAttribute(): bool
    {
        return $this->isAuthUserGroup();
    }

    public function beforeValidate()
    {
        if (!$this->name) {
            if (Settings::get('adopt_translated_names')) {
                if ($rlTranslate = post('RLTranslate')) {
                    // Return the first non-empty name in the translation array
                    array_walk_recursive($rlTranslate, function($value, $key) {
                        if (!$this->name && $key == 'name' && $value) $this->name = $value;
                    });
                }
            }
        }
    }

    public function beforeCreate()
    {
        // Auto provision a code
        // based on settings
        // if NULL
        if (!$this->code) {
            if ($autoProvisionCodes = Settings::get('auto_provision_codes')) {
                $this->code = Model::uniqueValue($this->name, 'code', self::class, $autoProvisionCodes);
            } else {
                // Allow unique exception to trigger
            }
        }
    }

    public static function menuitemCount(): mixed {
        # Auto-injected by acorn-create-system
        return self::count();
    }
}
