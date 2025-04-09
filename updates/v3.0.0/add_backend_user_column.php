<?php namespace Acorn\User\Updates;

use DB;
use Schema;
use Acorn\Migration;
use Acorn\User\Models\User;
use Backend\Models\User as BackendUser;

class AddBackendUserColumn extends Migration
{
    public function up()
    {
        // Add extra namespaced fields in to the users table
        Schema::table('backend_users', function(\Winter\Storm\Database\Schema\Blueprint $table) {
            if (!Schema::hasColumn($table->getTable(), 'acorn_user_user_id')) $table->uuid('acorn_user_user_id')->nullable();
            $table->foreign('acorn_user_user_id')->references('id')->on('acorn_user_users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('backend_users', function(\Winter\Storm\Database\Schema\Blueprint $table) {
            if (Schema::hasColumn($table->getTable(), 'acorn_user_user_id')) $table->dropColumn('acorn_user_user_id');
        });
    }
}
