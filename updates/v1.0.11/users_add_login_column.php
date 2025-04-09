<?php namespace AcornAssociated\User\Updates;

use Schema;
use AcornAssociated\Migration;
use AcornAssociated\User\Models\User;

class UsersAddLoginColumn extends Migration
{
    public function up()
    {
        Schema::table('acornassociated_user_users', function($table)
        {
            $table->string('login')->nullable()->index();
        });

        /*
         * Set login for existing users
         */
        $users = User::withTrashed()->get();
        foreach ($users as $user) {
            $user->login = $user->email;
            $user->save();
        }

        Schema::table('acornassociated_user_users', function($table)
        {
            $table->unique('login');
        });
    }

    public function down()
    {
        if (Schema::hasColumn('acornassociated_user_users', 'login')) {
            Schema::table('acornassociated_user_users', function($table)
            {
                $table->dropColumn('login');
            });
        }
    }
}
