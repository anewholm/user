<?php namespace Acorn\User\Updates;

use Schema;
use \Acorn\Migration;
use DB;

class CreateFunctions extends Migration
{
    public function up()
    {
        // Useful for DEFAULTS for created_by_user_id
        // especially when simple seeding data
        // TODO: SECURITY: is
        $this->createFunction('fn_acorn_user_get_seed_user', array(), 'uuid', array(), <<<SQL
            -- Intentional EXCEPTION if there is not one
            return (select uu.id
                from public.acorn_user_users uu
                where name = 'seeder' and is_system_user);
SQL
        );
    }

    public function down()
    {
        Schema::dropIfExists('fn_acorn_user_get_seed_user');
    }
}
