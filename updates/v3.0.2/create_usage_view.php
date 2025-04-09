<?php

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
        $this->createView('acornassociated_user_user_group_version_usages',
            <<<SQL
                SELECT NULL::uuid AS user_group_version_id,
                    NULL::character varying(1024) AS "table",
                    NULL::uuid AS id
SQL
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('acornassociated_user_user_group_version_usages');
    }
};
