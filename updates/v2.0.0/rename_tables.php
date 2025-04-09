<?php namespace Acorn\User\Updates;

use Db;
use Schema;
use Acorn\Migration;

class RenameTables extends Migration
{
    public function up()
    {
        $from = 'rainlab_user_mail_blockers';
        $to = 'acorn_user_mail_blockers';

        if (Schema::hasTable($from) && !Schema::hasTable($to)) {
            Schema::rename($from, $to);
        }

        Db::table('system_files')->where('attachment_type', 'RainLab\User\Models\User')->update(['attachment_type' => 'Acorn\User\Models\User']);
    }

    public function down()
    {
        $from = 'acorn_user_mail_blockers';
        $to = 'rainlab_user_mail_blockers';

        if (Schema::hasTable($from) && !Schema::hasTable($to)) {
            Schema::rename($from, $to);
        }

        Db::table('system_files')->where('attachment_type', 'Acorn\User\Models\User')->update(['attachment_type' => 'RainLab\User\Models\User']);
    }
}
