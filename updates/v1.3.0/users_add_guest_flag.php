<?php namespace AcornAssociated\User\Updates;

use Schema;
use AcornAssociated\Migration;

class UsersAddGuestFlag extends Migration
{
    public function up()
    {
        Schema::table('acornassociated_user_users', function($table)
        {
            $table->boolean('is_guest')->default(false);
        });
    }

    public function down()
    {
        if (Schema::hasColumn('acornassociated_user_users', 'is_guest')) {
            Schema::table('acornassociated_user_users', function($table)
            {
                $table->dropColumn('is_guest');
            });
        }
    }
}
