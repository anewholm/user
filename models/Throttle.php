<?php namespace Acorn\User\Models;

use Winter\Storm\Auth\Models\Throttle as ThrottleBase;

class Throttle extends ThrottleBase
{
    use \Illuminate\Database\Eloquent\Concerns\HasUuids;

    /**
     * @var string The database table used by the model.
     */
    protected $table = 'acorn_user_throttle';

    /**
     * @var array Relations
     */
    public $belongsTo = [
        'user' => User::class
    ];
}
