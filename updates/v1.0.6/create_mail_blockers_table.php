<?php namespace Acorn\User\Updates;

use DB;
use Schema;
use Acorn\Migration;

class CreateMailBlockersTable extends Migration
{

    public function up()
    {
        Schema::create('rainlab_user_mail_blockers', function($table)
        {
            $table->engine = 'InnoDB';
            $table->uuid('id')->primary()->default(DB::raw('(gen_random_uuid())'));
            $table->string('email')->index()->nullable();
            $table->string('template')->index()->nullable();
            $table->uuid('user_id')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('rainlab_user_mail_blockers');
    }

}
