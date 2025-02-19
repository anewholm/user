<?php namespace AcornAssociated\User\Models;

use Winter\Storm\Auth\Models\Group as GroupBase;
use ApplicationException;

class UserGroupType extends GroupBase
{
    use \Illuminate\Database\Eloquent\Concerns\HasUuids;
    use \AcornAssociated\Traits\PathsHelper;
    use \AcornAssociated\Backendlocalization\Class\TranslateBackend;

    public $implement = ['Winter.Translate.Behaviors.TranslatableModel'];
    public $translatable = ['name', 'description'];

    /**
     * @var string The database table used by the model.
     */
    protected $table = 'acornassociated_user_user_group_types';

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
        'groups'       => [UserGroup::class, 'key' => 'type_id'],
    ];
    public $belongsToMany = [];
    public $belongsTo = [];

    /**
     * @var array The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
    ];

    public $timestamps = FALSE;

    public function getGroupsCountAttribute()
    {
        // $this->groups implements tree things so a hasMany does not work
        return $this->groups->count();
    }

    public static function dropdownOptions($form, $field)
    {
        return \AcornAssociated\Model::dropdownOptions($form, $field, self::class);
    }

    public function getFullyQualifiedNameAttribute()
    {
        return $this->name;
    }
}
