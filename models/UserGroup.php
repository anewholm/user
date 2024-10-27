<?php namespace Acorn\User\Models;

use Winter\Storm\Auth\Models\Group as GroupBase;
use ApplicationException;

/**
 * User Group Model
 */
class UserGroup extends GroupBase
{
    use \Illuminate\Database\Eloquent\Concerns\HasUuids;
    use \Acorn\Traits\PathsHelper;

    const GROUP_GUEST = 'guest';
    const GROUP_REGISTERED = 'registered';

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
    public $belongsToMany = [
        'users'       => [User::class, 'table' => 'acorn_user_user_group'],
        'users_count' => [User::class, 'table' => 'acorn_user_user_group', 'count' => true]
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
    public static function dropdownOptions($form, $field)
    {
        return \Acorn\Model::dropdownOptions($form, $field, self::class);
    }

    public function getFullyQualifiedNameAttribute()
    {
        return $this->name;
    }
}
