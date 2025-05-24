<?php

namespace Acorn\User\Updates;

use DB;
use Schema;
use Acorn\Migration;

class CreateUserGroupsTable extends Migration
{
    static protected $table = 'acorn_user_user_groups';

    public function up()
    {   if(!Schema::hasTable(self::$table)){
        Schema::create(self::$table, function ($table) {
            $table->engine = 'InnoDB';
            $table->uuid('id')->primary()->default(DB::raw('(gen_random_uuid())'));
            $table->string('name');
            $table->string('code')->nullable()->index()->unique();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->uuid('parent_user_group_id')->nullable();
            $table->integer('nest_left')->nullable();
            $table->integer('nest_right')->nullable();
            $table->integer('nest_depth')->nullable();
            // Colour and Images
            $table->string('image',  1024)->nullable();
            $table->string('colour', 1024)->nullable();
        });

        Schema::table(self::$table, function (\Winter\Storm\Database\Schema\Blueprint $table) {
            // Create after main create because it is self-referencing
            $table->foreign('parent_user_group_id')
                ->references('id')->on(self::$table)
                ->onDelete('set null');
        });
    }

        Schema::create('acorn_user_user_group', function ($table) {
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
