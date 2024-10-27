<?php namespace Acorn\User\Updates;

use Schema;
use Acorn\Migration;

class UsersAddSurname extends Migration
{
    public function up()
    {
        Schema::table('acorn_user_users', function($table)
        {
            $table->string('surname')->nullable();
        });
    }

    public function down()
    {
        if (Schema::hasColumn('acorn_user_users', 'surname')) {
            Schema::table('acorn_user_users', function($table)
            {
                $table->dropColumn('surname');
            });
        }
    }
}
