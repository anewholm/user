<?php namespace AcornAssociated\User\Updates;

use DB;
use Schema;
use AcornAssociated\Migration;

class CreateUserGroupTypesTable extends Migration
{

    public function up()
    {
        Schema::create('acornassociated_user_user_group_types', function($table)
        {
            $table->engine = 'InnoDB';
            $table->uuid('id')->primary()->default(DB::raw('(gen_random_uuid())'));
            $table->string('name')->nullable();
            $table->string('description')->nullable();
            $table->string('colour', 1024)->nullable();
            $table->string('image',  1024)->nullable();
            $table->timestamps();
        });

        Schema::table('acornassociated_user_user_groups', function(\Winter\Storm\Database\Schema\Blueprint $table) {
            if (!Schema::hasColumn($table->getTable(), 'type_id')) $table->uuid('type_id')->nullable();
            $table->foreign('type_id')->references('id')->on('acornassociated_user_user_group_types')->onDelete('set null');
        });
    }

    public function down()
    {
        // TODO: Drop the FK first: Schema::dropIfExists('acornassociated_user_user_groups.type_id');
        Schema::dropIfExists('acornassociated_user_user_group_types');
    }

}
