<?php namespace AcornAssociated\User\Updates;

use DB;
use Schema;
use AcornAssociated\Migration;

class CreateThrottleTable extends Migration
{

    public function up()
    {
        Schema::create('acornassociated_user_throttle', function($table)
        {
            $table->engine = 'InnoDB';
            $table->uuid('id')->primary()->default(DB::raw('(gen_random_uuid())'));
            $table->uuid('user_id')->nullable()->index();
            $table->string('ip_address')->nullable()->index();
            $table->integer('attempts')->default(0);
            $table->timestamp('last_attempt_at')->nullable();
            $table->boolean('is_suspended')->default(0);
            $table->timestamp('suspended_at')->nullable();
            $table->boolean('is_banned')->default(0);
            $table->timestamp('banned_at')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('acornassociated_user_throttle');
    }

}
