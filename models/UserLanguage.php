<?php namespace Acorn\User\Models;

use Acorn\Model;

class UserLanguage extends Model
{
    use \Illuminate\Database\Eloquent\Concerns\HasUuids;
    use \Acorn\Traits\PathsHelper;

    /**
     * @var string The database table used by the model.
     */
    protected $table = 'acorn_user_user_languages';

    public $implement = ['Acorn.Behaviors.TranslatableModel'];
    public $implementReplaces = ['Winter.Translate.Behaviors.TranslatableModel'];
    public $translatable = [
        'language[name]'
    ];

    /**
     * Validation rules
     */
    public $rules = [];

    public $timestamps = FALSE;

    /**
     * @var array Relations
     */
    public $belongsTo = [
        // Overwrite the "Winter\Storm\Auth\Models\Role" association
        // because its public.roles table does not exist
        'language'  => [Language::class,  'table' => 'acorn_user_languages'],
        'user'      => [User::class,      'table' => 'acorn_users'],
    ];
    public $belongsToMany = [];

    public $attachOne = [];

    /**
     * @var array The attributes that are mass assignable.
     */
    protected $fillable = [];

    /**
     * Reset guarded fields, because we use $fillable instead.
     * @var array The attributes that aren't mass assignable.
     */
    protected $guarded = [];


    /**
     * Purge attributes from data set.
     */
    protected $dates = [];

    public function getNameAttribute()
    {
        $primaryString = trans('acorn.user::lang.models.userlanguage.current');
        $currentString = ($this->current ? " ($primaryString)" : '');
        $languageName  = $this->language->name;
        return "$languageName$currentString";
    }

    public static function menuitemCount(): mixed {
        # Auto-injected by acorn-create-system
        return self::count();
    }
}
