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

        // Seed user
        $seedUser = User::create(['name' => 'seeder', 'is_system_user' => TRUE]);

        // Attach user to admin: admin must create stuff
        $adminUser        = User::create(['name' => 'admin', 'is_system_user' => TRUE]);
        $adminBackendUser = BackendUser::where('login', 'admin')
                                       ->where('is_superuser', true)
                                       ->first();
        if ($adminBackendUser) {
            $adminBackendUser->acornassociated_user_user_id = $adminUser->id;
            $adminBackendUser->save();
        } else throw new \Exception('Superuser admin not found when trying to associated with AcornAssociated\User');
    }

    public function down()
    {
        Schema::table('backend_users', function(\Winter\Storm\Database\Schema\Blueprint $table) {
            if (Schema::hasColumn($table->getTable(), 'acornassociated_user_user_id')) $table->dropColumn('acornassociated_user_user_id');
        });
    }
}
