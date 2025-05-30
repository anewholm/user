<?php

use Winter\Storm\Database\Schema\Blueprint;
use Acorn\Migration;
use Winter\Storm\Support\Facades\Schema;

class CreateUserGroupVersioning extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('acorn_user_user_group_versions', function($table)
        {
            $table->engine = 'InnoDB';
            $table->uuid('id')->primary()->default(DB::raw('(gen_random_uuid())'));
            $table->uuid('user_group_id');
            $table->integer('version')->default(1);
            $table->boolean('current')->default(true);
            $table->string('import_source', 1024)->nullable();
            $table->foreign('user_group_id')
                ->references('id')->on('acorn_user_user_groups')
                ->onDelete('cascade');
        });

        Schema::create('acorn_user_user_group_version', function($table)
        {
            $table->uuid('user_id');
            $table->uuid('user_group_version_id');
            $table->primary(array('user_id', 'user_group_version_id'));
            $table->foreign('user_group_version_id')
                ->references('id')->on('acorn_user_user_group_versions')
                ->onDelete('cascade');
            $table->foreign('user_id')
                ->references('id')->on('acorn_user_users')
                ->onDelete('cascade');
        });

        $this->createFunctionAndTrigger('acorn_user_user_group_version', 
            'BEFORE', 
            'INSERT', 
            'public.acorn_user_user_group_versions', 
            TRUE, 
            [],
        <<<SQL
            select coalesce(max(version), 0) + 1 into new.version 
                from acorn_user_user_group_versions 
                where user_group_id = new.user_group_id;
            update acorn_user_user_group_versions 
                set current = false
                where user_group_id = new.user_group_id and current;
            
            return new;
SQL
        );

        $this->createFunctionAndTrigger('acorn_user_user_group_first_version', 
            'AFTER', 
            'INSERT', 
            'public.acorn_user_user_groups', 
            TRUE, 
            [],
        <<<SQL
            insert into acorn_user_user_group_versions(user_group_id)
                values(new.id);
            return new;
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
        Schema::dropIfExists('acorn_user_user_group_version');
        Schema::dropIfExists('acorn_user_user_group_versions');
    }
};
