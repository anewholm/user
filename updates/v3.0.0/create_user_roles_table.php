<?php namespace AcornAssociated\User\Updates;

use DB;
use Schema;
use AcornAssociated\Migration;

class CreateUserRolesTable extends Migration
{

    public function up()
    {
        Schema::create('acornassociated_user_roles', function($table)
        {
            $table->engine = 'InnoDB';
            $table->uuid('id')->primary()->default(DB::raw('(gen_random_uuid())'));
            $table->string('name')->nullable();
            $table->text('permissions')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('acornassociated_user_roles');
    }

}
