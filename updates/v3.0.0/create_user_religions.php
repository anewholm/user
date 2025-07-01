<?php namespace Acorn\User\Updates;

use DB;
use Schema;
use Acorn\Migration;

class CreateUserReligions extends Migration
{

    public function up()
    {
        Schema::create('acorn_user_religions', function($table)
        {
            $table->engine = 'InnoDB';
            $table->uuid('id')->primary()->default(DB::raw('(gen_random_uuid())'));
            $table->string('name', 1024)->unique();
        });

        Schema::table('acorn_user_users', function(\Winter\Storm\Database\Schema\Blueprint $table) {
            if (!Schema::hasColumn($table->getTable(), 'religion_id')) $table->uuid('religion_id')->nullable();
            $table->foreign('religion_id')->references('id')->on('acorn_user_religions')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('acorn_user_users', function(\Winter\Storm\Database\Schema\Blueprint $table) {
            if (Schema::hasColumn($table->getTable(), 'religion_id')) $table->dropColumn('religion_id');
        });
        Schema::dropIfExists('acorn_user_religions');
    }

}
