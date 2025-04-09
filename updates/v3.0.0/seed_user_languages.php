<?php namespace Acorn\User\Updates;

use Acorn\User\Models\Language;
use Winter\Storm\Database\Updates\Seeder;

class SeedUserLanguages extends Seeder
{
    public function run()
    {
        Language::create([
            'name' => 'English',
        ]);

        Language::create([
            'name' => 'Kurdish',
        ]);

        Language::create([
            'name' => 'Arabic',
        ]);
    }
}
