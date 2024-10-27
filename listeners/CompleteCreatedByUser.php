<?php namespace Acorn\User\Listeners;

use BackendAuth;
use \Acorn\Events\ModelBeforeSave;
use \Acorn\User\Models\User;

class CompleteCreatedByUser
{
    public function handle(ModelBeforeSave &$MBS)
    {
        $model = &$MBS->model;

        if (isset($model->belongsTo['created_by_user']) && !$model->created_by_user) {
            if ($authUser = User::authUser()) $model->created_by_user = $authUser;
            else throw new \Exception("Auth user is not associated with a User for created_by_user value");
        }
    }
}
