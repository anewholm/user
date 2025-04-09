<?php namespace AcornAssociated\User\Updates;

use Schema;
use AcornAssociated\Migration;

class UsersAddDeletedAt extends Migration
{
    public function up()
    {
        Schema::table('acornassociated_user_users', function($table)
        {
            $table->timestamp('deleted_at')->nullable();
        });
    }

    public function down()
    {
        if (Schema::hasColumn('acornassociated_user_users', 'deleted_at')) {
            Schema::table('acornassociated_user_users', function($table)
            {
                $table->dropColumn('deleted_at');
            });
        }
    }
}
