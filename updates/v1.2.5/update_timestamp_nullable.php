<?php namespace AcornAssociated\User\Updates;

use AcornAssociated\Migration;
use DbDongle;

class UpdateTimestampsNullable extends Migration
{
    public function up()
    {
        DbDongle::disableStrictMode();

        DbDongle::convertTimestamps('acornassociated_user_users');
        DbDongle::convertTimestamps('acornassociated_user_groups');
        DbDongle::convertTimestamps('rainlab_user_mail_blockers');
    }

    public function down()
    {
        // ...
    }
}
