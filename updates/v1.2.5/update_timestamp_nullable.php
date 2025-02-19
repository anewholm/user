<?php namespace Acorn\User\Updates;

use Acorn\Migration;
use DbDongle;

class UpdateTimestampsNullable extends Migration
{
    public function up()
    {
        DbDongle::disableStrictMode();

        DbDongle::convertTimestamps('acorn_user_users');
        DbDongle::convertTimestamps('acorn_user_groups');
        DbDongle::convertTimestamps('rainlab_user_mail_blockers');
    }

    public function down()
    {
        // ...
    }
}
