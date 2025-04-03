<?php

use Winter\Storm\Database\Schema\Blueprint;
use Acorn\Migration;
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
        $this->createView('acorn_user_user_group_version_usages',
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
        Schema::dropIfExists('acorn_user_user_group_version_usages');
    }
};
