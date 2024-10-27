<?php namespace Acorn\User\Updates;

use Schema;
use Acorn\Migration;
use Acorn\User\Models\User;

class UsersAddLoginColumn extends Migration
{
    public function up()
    {
        Schema::table('acorn_user_users', function($table)
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

        Schema::table('acorn_user_users', function($table)
        {
            $table->unique('login');
        });
    }

    public function down()
    {
        if (Schema::hasColumn('acorn_user_users', 'login')) {
            Schema::table('acorn_user_users', function($table)
            {
                $table->dropColumn('login');
            });
        }
    }
}
