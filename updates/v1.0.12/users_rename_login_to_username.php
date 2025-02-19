<?php namespace AcornAssociated\User\Updates;

use Schema;
use AcornAssociated\Migration;

class UsersRenameLoginToUsername extends Migration
{

    public function up()
    {
        Schema::table('acornassociated_user_users', function($table)
        {
            $table->renameColumn('login', 'username');
        });
    }

    public function down()
    {
        if (Schema::hasColumn('acornassociated_user_users', 'login')) {
            Schema::table('acornassociated_user_users', function($table)
            {
                $table->renameColumn('username', 'login');
            });
        }
    }
}
