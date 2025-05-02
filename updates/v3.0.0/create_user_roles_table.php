<?php namespace Acorn\User\Updates;

use DB;
use Schema;
use Acorn\Migration;

class CreateUserRolesTable extends Migration
{

    public function up()
    {
        Schema::create('acorn_user_roles', function($table)
        {
            $table->engine = 'InnoDB';
            $table->uuid('id')->primary()->default(DB::raw('(gen_random_uuid())'));
            $table->string('name')->nullable();
            $table->text('permissions')->nullable();
            $table->timestamps();
        });

        Schema::create('acorn_user_role_user', function($table)
        {
            // X-X pivot
            $table->engine = 'InnoDB';
            $table->uuid('user_id');
            $table->uuid('role_id');

            $table->foreign('user_id')
                ->references('id')->on('acorn_user_users')
                ->onDelete('cascade');
            $table->foreign('role_id')
                ->references('id')->on('acorn_user_roles')
                ->onDelete('cascade');
        });
}

    public function down()
    {
        Schema::dropIfExists('acorn_user_role_user');
        Schema::dropIfExists('acorn_user_roles');
    }

}
