<?php namespace Acorn\User\Updates;

use DB;
use Schema;
use Acorn\Migration;

class CreateUserGroupTypesTable extends Migration
{

    public function up()
    {
        Schema::create('acorn_user_user_group_types', function($table)
        {
            $table->engine = 'InnoDB';
            $table->uuid('id')->primary()->default(DB::raw('(gen_random_uuid())'));
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('colour', 1024)->nullable();
            $table->string('image',  1024)->nullable();
            $table->timestamps();
        });

        Schema::table('acorn_user_user_groups', function(\Winter\Storm\Database\Schema\Blueprint $table) {
            if (!Schema::hasColumn($table->getTable(), 'type_id')) $table->uuid('type_id')->nullable();
            $table->foreign('type_id')->references('id')->on('acorn_user_user_group_types')->onDelete('set null');
        });
    }

    public function down()
    {
        // TODO: Drop the FK first: Schema::dropIfExists('acorn_user_user_groups.type_id');
        Schema::dropIfExists('acorn_user_user_group_types');
    }

}
