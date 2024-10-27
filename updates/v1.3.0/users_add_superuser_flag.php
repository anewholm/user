<?php namespace Acorn\User\Updates;

use Schema;
use Acorn\Migration;

class UsersAddSuperuserFlag extends Migration
{
    public function up()
    {
        Schema::table('acorn_user_users', function($table)
        {
            $table->boolean('is_superuser')->default(false);
        });
    }

    public function down()
    {
        if (Schema::hasColumn('acorn_user_users', 'is_superuser')) {
            Schema::table('acorn_user_users', function($table)
            {
                $table->dropColumn('is_superuser');
            });
        }
    }
}
