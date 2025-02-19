<?php namespace Acorn\User\Updates;

use DB;
use Schema;
use Acorn\Migration;

class CreateUserLanguages extends Migration
{

    public function up()
    {
        Schema::create('acorn_user_languages', function($table)
        {
            $table->engine = 'InnoDB';
            $table->uuid('id')->primary()->default(DB::raw('(gen_random_uuid())'));
            $table->string('name', 1024)->unique();
            $table->timestamps();
        });

        Schema::create('acorn_user_language_user', function($table)
        {
            $table->engine = 'InnoDB';
            // TODO: Do Winter pivot tables include ids? I think yes...
            $table->uuid('user_id');
            $table->uuid('language_id');
            $table->timestamps();

            $table->primary(['user_id','language_id']);
            $table->foreign('user_id')->references('id')->on('acorn_user_users');
            $table->foreign('language_id')->references('id')->on('acorn_user_languages');
        });
    }

    public function down()
    {
        Schema::dropIfExists('acorn_user_language_user');
        Schema::dropIfExists('acorn_user_languages');
    }

}
