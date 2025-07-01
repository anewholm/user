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
            $table->string('locale', 10)->unique();
        });

        Schema::create('acorn_user_user_languages', function($table)
        {
            $table->engine = 'InnoDB';
            // TODO: Do Winter pivot tables include ids? I think yes...
            $table->uuid('id')->primary()->default(DB::raw('(gen_random_uuid())'));
            $table->uuid('user_id');
            $table->uuid('language_id');
            $table->boolean('current')->default(true);

            $table->foreign('user_id')->references('id')->on('acorn_user_users')->onDelete('CASCADE');
            $table->foreign('language_id')->references('id')->on('acorn_user_languages')->onDelete('CASCADE');
        });


        $this->createFunctionAndTrigger('acorn_user_user_languages_current', 
            'AFTER', 
            'INSERT OR UPDATE', 
            'acorn_user_user_languages', 
            TRUE, 
            [],
        <<<SQL
            -- Enforce only one current
            -- False may be explicitly specified, for example, importing old codes
            -- Column default should be true on inserts
            if new.current then
                -- Unset the old current(s)
                update acorn_user_user_languages
                    set "current" = false
                    where user_id = new.user_id
                    and "current"
                    and id != new.id; 
            end if;
            return new;
SQL
        );  
    }

    public function down()
    {
        Schema::dropIfExists('acorn_user_user_languages');
        Schema::dropIfExists('acorn_user_languages');
    }

}
