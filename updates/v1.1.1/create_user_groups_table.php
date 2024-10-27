<?php namespace Acorn\User\Updates;

use DB;
use Schema;
use Acorn\Migration;

class CreateUserGroupsTable extends Migration
{

    public function up()
    {
        Schema::create('acorn_user_user_groups', function($table)
        {
            $table->engine = 'InnoDB';
            $table->uuid('id')->primary()->default(DB::raw('(gen_random_uuid())'));
            $table->string('name');
            $table->string('code')->nullable()->index();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('acorn_user_user_group', function($table)
        {
            $table->engine = 'InnoDB';
            $table->uuid('user_id');
            $table->uuid('user_group_id');
            $table->primary(['user_id', 'user_group_id'], 'user_group');
        });
    }

    public function down()
    {
        Schema::dropIfExists('acorn_user_user_groups');
        Schema::dropIfExists('acorn_user_user_group');
    }

}
