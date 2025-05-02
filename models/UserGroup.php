<?php namespace Acorn\User\Models;

use Winter\Storm\Auth\Models\Group as GroupBase;
use ApplicationException;
use Acorn\Collection;
use Winter\Storm\Database\TreeCollection;

/**
 * User Group Model
 */
class UserGroup extends GroupBase
{
    use \Illuminate\Database\Eloquent\Concerns\HasUuids;
    use \Acorn\Traits\PathsHelper;
    use \Winter\Storm\Database\Traits\NestedTree;
    use \Acorn\Backendlocalization\Class\TranslateBackend;
    use \Staudenmeir\EloquentHasManyDeep\HasTableAlias;

    const PARENT_ID = 'parent_user_group_id';

    const GROUP_GUEST = 'guest';
    const GROUP_REGISTERED = 'registered';

    public $implement = ['Winter.Translate.Behaviors.TranslatableModel'];
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
        'code' => 'required|regex:/^[a-zA-Z0-9_\-]+$/|unique:acorn_user_user_groups',
    ];

    /**
     * @var array Relations
     */
    public $hasMany = [
        'children' => [UserGroup::class, 'key' => 'parent_user_group_id', 'alias' => 'test', 'table' => 'test'],
    ];
    public $belongsToMany = [
        'users'       => [User::class, 'table' => 'acorn_user_user_group'],
        'users_count' => [User::class, 'table' => 'acorn_user_user_group', 'count' => true]
    ];
    public $belongsTo = [
        'parent_user_group' => [UserGroup::class, 'key' => 'parent_user_group_id', 'alias' => 'test', 'table' => 'test'], 
        'type' => UserGroupType::class,
    ];

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

    public static function dropdownOptions($form, $field)
    {
        return \Acorn\Model::dropdownOptions($form, $field, self::class);
    }
}
