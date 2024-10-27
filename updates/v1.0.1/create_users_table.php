<?php namespace Acorn\User\Updates;

use DB;
use Schema;
use Acorn\Migration;

class CreateUsersTable extends Migration
{

    public function up()
    {
        Schema::create('acorn_user_users', function($table)
        {
            $table->engine = 'InnoDB';
            $table->uuid('id')->primary()->default(DB::raw('(gen_random_uuid())'));
            $table->string('name')->nullable();
            // email & password are now nullable because of non-front-end settings
            $table->string('email')->nullable()->unique();
            $table->string('password')->nullable();
            $table->string('activation_code')->nullable()->index();
            $table->string('persist_code')->nullable();
            $table->string('reset_password_code')->nullable()->index();
            $table->text('permissions')->nullable();
            $table->boolean('is_activated')->default(0);
            $table->boolean('system_user')->default(0);
            $table->timestamp('activated_at')->nullable();
            $table->timestamp('last_login')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('acorn_user_users');
    }

}
