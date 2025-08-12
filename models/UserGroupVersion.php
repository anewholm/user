<?php namespace Acorn\User\Models;

use Acorn\Model;

/**
 * User Group Version Model
 */
class UserGroupVersion extends Model
{
    /* Generated Fields:
     * id(uuid)
     * user_group_id(uuid)
     * version(int)  - trigger updated
     * current(bool) - trigger updated
     */

    use \Illuminate\Database\Eloquent\Concerns\HasUuids;
    use \Acorn\Traits\PathsHelper;
    use \Staudenmeir\EloquentHasManyDeep\HasTableAlias;

    /**
     * @var string The database table used by the model.
     */
    protected $table = 'acorn_user_user_group_versions';

    /**
     * @var array Relations
     */
    public $hasMany = [];
    public $belongsToMany = [
        'users'       => [User::class, 'table' => 'acorn_user_user_group_version'],
        'users_count' => [User::class, 'table' => 'acorn_user_user_group_version', 'count' => true]
    ];
    public $belongsTo = [
        'user_group' => UserGroup::class, 
    ];

    public function getNameAttribute(): string
    {
        $groupName = $this->user_group->name;
        return "$groupName (v$this->version)";
    }

    public static function menuitemCount(): mixed {
        return self::count();
    }
}
