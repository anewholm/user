<?php namespace Acorn\User\Updates;

use Carbon\Carbon;
use Schema;
use Acorn\Migration;

class UsersAddIpAddress extends Migration
{
    public function up()
    {
        Schema::table('acorn_user_users', function($table)
        {
            $table->string('created_ip_address')->nullable();
            $table->string('last_ip_address')->nullable();
        });
    }

    public function down()
    {
        if (Schema::hasColumn('acorn_user_users', 'created_ip_address')) {
            Schema::table('acorn_user_users', function($table)
            {
                $table->dropColumn('created_ip_address');
                $table->dropColumn('last_ip_address');
            });
        }
    }
}
