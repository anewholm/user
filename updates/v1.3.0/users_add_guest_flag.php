<?php namespace Acorn\User\Updates;

use Schema;
use Acorn\Migration;

class UsersAddGuestFlag extends Migration
{
    public function up()
    {
        Schema::table('acorn_user_users', function($table)
        {
            $table->boolean('is_guest')->default(false);
        });
    }

    public function down()
    {
        if (Schema::hasColumn('acorn_user_users', 'is_guest')) {
            Schema::table('acorn_user_users', function($table)
            {
                $table->dropColumn('is_guest');
            });
        }
    }
}
