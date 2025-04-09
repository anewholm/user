<?php namespace Acorn\User\Updates;

use Schema;
use Acorn\Migration;
use Acorn\User\Models\User;

class UsersAddLastSeen extends Migration
{
    public function up()
    {
        Schema::table('acorn_user_users', function($table)
        {
            $table->timestamp('last_seen')->nullable();
        });
    }

    public function down()
    {
        if (Schema::hasColumn('acorn_user_users', 'last_seen')) {
            Schema::table('acorn_user_users', function($table)
            {
                $table->dropColumn('last_seen');
            });
        }
    }
}
