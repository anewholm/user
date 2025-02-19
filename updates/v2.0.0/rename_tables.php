<?php namespace AcornAssociated\User\Updates;

use Db;
use Schema;
use AcornAssociated\Migration;

class RenameTables extends Migration
{
    public function up()
    {
        $from = 'rainlab_user_mail_blockers';
        $to = 'acornassociated_user_mail_blockers';

        if (Schema::hasTable($from) && !Schema::hasTable($to)) {
            Schema::rename($from, $to);
        }

        Db::table('system_files')->where('attachment_type', 'RainLab\User\Models\User')->update(['attachment_type' => 'AcornAssociated\User\Models\User']);
    }

    public function down()
    {
        $from = 'acornassociated_user_mail_blockers';
        $to = 'rainlab_user_mail_blockers';

        if (Schema::hasTable($from) && !Schema::hasTable($to)) {
            Schema::rename($from, $to);
        }

        Db::table('system_files')->where('attachment_type', 'AcornAssociated\User\Models\User')->update(['attachment_type' => 'RainLab\User\Models\User']);
    }
}
