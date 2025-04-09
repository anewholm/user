<?php namespace Acorn\User\Updates;

use Schema;
use Acorn\Migration;

class UsersRenameLoginToUsername extends Migration
{

    public function up()
    {
        Schema::table('acorn_user_users', function($table)
        {
            $table->renameColumn('login', 'username');
        });
    }

    public function down()
    {
        if (Schema::hasColumn('acorn_user_users', 'login')) {
            Schema::table('acorn_user_users', function($table)
            {
                $table->renameColumn('username', 'login');
            });
        }
    }
}
