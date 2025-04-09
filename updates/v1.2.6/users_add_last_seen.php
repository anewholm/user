<?php namespace AcornAssociated\User\Updates;

use Schema;
use AcornAssociated\Migration;
use AcornAssociated\User\Models\User;

class UsersAddLastSeen extends Migration
{
    public function up()
    {
        Schema::table('acornassociated_user_users', function($table)
        {
            $table->timestamp('last_seen')->nullable();
        });
    }

    public function down()
    {
        if (Schema::hasColumn('acornassociated_user_users', 'last_seen')) {
            Schema::table('acornassociated_user_users', function($table)
            {
                $table->dropColumn('last_seen');
            });
        }
    }
}
