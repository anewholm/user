<?php

use Twig\Node\BodyNode;
use Winter\Storm\Database\Schema\Blueprint;
use AcornAssociated\Migration;
use Winter\Storm\Support\Facades\Schema;

class CreateFunctions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->createFunction('fn_acornassociated_user_get_seed_user', 
            [], 
            'uuid', 
            [
                'user_id uuid',
            ], 
        <<<BODY
            -- Lazy create the seeder user
            select into user_id uu.id 
                from public.acornassociated_user_users uu
                where name = 'seeder' and is_system_user limit 1;
            if user_id is null then
                insert into public.acornassociated_user_users(name, is_system_user)
                    values('seeder', true) 
                    returning id into user_id;
            end if;
            
            
            return user_id;
BODY
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fn_acornassociated_user_get_seed_user');
    }
};
