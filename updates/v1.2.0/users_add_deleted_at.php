<?php namespace Acorn\User\Updates;

use Schema;
use Acorn\Migration;

class UsersAddDeletedAt extends Migration
{
    public function up()
    {
        Schema::table('acorn_user_users', function($table)
        {
            $table->timestamp('deleted_at')->nullable();
        });
    }

    public function down()
    {
        if (Schema::hasColumn('acorn_user_users', 'deleted_at')) {
            Schema::table('acorn_user_users', function($table)
            {
                $table->dropColumn('deleted_at');
            });
        }
    }
}
