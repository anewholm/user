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
            $table->string('name');                // first name
            $table->string('surname')->nullable(); // last name
            // email & password are now nullable because of non-front-end settings
            $table->string('email')->nullable()->unique();
            $table->string('password')->nullable();
            
            $table->string('activation_code')->nullable()->index();
            $table->string('persist_code')->nullable();
            $table->string('reset_password_code')->nullable()->index();
            $table->text('permissions')->nullable();
            $table->boolean('is_activated')->default(0);
            $table->boolean('is_system_user')->default(0); // system_user is a Postgres reserved word
            $table->timestamp('activated_at')->nullable();
            $table->timestamp('last_login')->nullable();
            
            // New fields. All nullable in case user is not for this purpose
            // TODO: ALTER TABLE IF EXISTS public.acorn_user_users
            //     ADD CONSTRAINT gender_enum CHECK (gender is null or gender = ANY (ARRAY['M'::"char", 'F'::"char", 'O'::"char"]));
            // TODO: ALTER TABLE IF EXISTS public.acorn_user_users
            //     ADD CONSTRAINT marital_status_enum CHECK (marital_status is null or marital_status = ANY (ARRAY['M'::"char", 'S'::"char", 'O'::"char"]));
            $table->timestamp('birth_date')->nullable();
            $table->string('fathers_name', 1024)->nullable();
            $table->string('mothers_name', 1024)->nullable();
            $table->enum('gender', ['M', 'F', 'O'])->nullable();
            $table->enum('marital_status', ['M', 'S', 'O'])->nullable();
            $table->string('import_source', 1024)->nullable()->unique();

            $table->timestamps();
        });

        // Mostly to spot import errors, and allow on conflict(unique_user) do nothing
        $this->addUniqueConstraint('acorn_user_users', array(
            'name', 'surname', 'birth_date', 'fathers_name', 'mothers_name', 'gender'
        ), 'unique_user');
    }

    public function down()
    {
        Schema::dropIfExists('acorn_user_users');
    }

}
