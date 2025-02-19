<?php namespace AcornAssociated\User\Updates;

use Schema;
use AcornAssociated\Migration;

class UsersAddSurname extends Migration
{
    public function up()
    {
        Schema::table('acornassociated_user_users', function($table)
        {
            $table->string('surname')->nullable();
        });
    }

    public function down()
    {
        if (Schema::hasColumn('acornassociated_user_users', 'surname')) {
            Schema::table('acornassociated_user_users', function($table)
            {
                $table->dropColumn('surname');
            });
        }
    }
}
