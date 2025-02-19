<?php

use Twig\Node\BodyNode;
use Winter\Storm\Database\Schema\Blueprint;
use AcornAssociated\Migration;
use Winter\Storm\Support\Facades\Schema;

class CreateUsageView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // TODO: Create fn_acornassociated_user_get_seed_user
        $this->createFunction('fn_acornassociated_user_get_seed_user', [], 'uuid', [], 
        <<<BODY
            -- We select the first user in the system
            -- Intentional EXCEPTION if there is not one
            return (select uu.id 
                --from public.backend_users bu
                --inner join public.acornassociated_user_users uu on bu.acornassociated_user_user_id = uu.id
                --where bu.is_superuser
                from public.acornassociated_user_users uu
                limit 1);
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
    }
};
