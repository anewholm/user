<?php namespace AcornAssociated\User\Updates;

use DB;
use Schema;
use AcornAssociated\Migration;
use AcornAssociated\User\Models\User;
use Backend\Models\User as BackendUser;

class AddBackendUserColumn extends Migration
{
    public function up()
    {
        // Add extra namespaced fields in to the users table
        Schema::table('backend_users', function(\Winter\Storm\Database\Schema\Blueprint $table) {
            if (!Schema::hasColumn($table->getTable(), 'acornassociated_user_user_id')) $table->uuid('acornassociated_user_user_id')->nullable();
            $table->foreign('acornassociated_user_user_id')->references('id')->on('acornassociated_user_users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('backend_users', function(\Winter\Storm\Database\Schema\Blueprint $table) {
            if (Schema::hasColumn($table->getTable(), 'acornassociated_user_user_id')) $table->dropColumn('acornassociated_user_user_id');
        });
    }
}
