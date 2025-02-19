<?php namespace AcornAssociated\User\Updates;

use Schema;
use \AcornAssociated\Migration;
use DB;

class CreateFunctions extends Migration
{
    public function up()
    {
        // Useful for DEFAULTS for created_by_user_id
        // especially when simple seeding data
        // TODO: SECURITY: is
        $this->createFunction('fn_acornassociated_user_get_seed_user', array(), 'uuid', array(), <<<SQL
            -- Intentional EXCEPTION if there is not one
            return (select uu.id
                from public.acornassociated_user_users uu
                where name = 'seeder' and is_system_user);
SQL
        );
    }

    public function down()
    {
        Schema::dropIfExists('fn_acornassociated_user_get_seed_user');
    }
}
