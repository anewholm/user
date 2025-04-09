<?php namespace AcornAssociated\User\Updates;

use AcornAssociated\User\Models\UserGroupType;
use Winter\Storm\Database\Updates\Seeder;

class SeedUserGroupTypes extends Seeder
{
    public function run()
    {
        UserGroupType::create([
            'name' => 'Office',
        ]);
    }
}
