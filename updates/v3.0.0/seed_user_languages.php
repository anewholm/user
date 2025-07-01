<?php namespace Acorn\User\Updates;

use Acorn\User\Models\Language;
use Winter\Storm\Database\Updates\Seeder;

class SeedUserLanguages extends Seeder
{
    public function run()
    {
        Language::create([
            'id'   => '9eaa5c4d-9080-4799-afa7-3741349b5beb',
            'name' => 'English',
            'locale' => 'en'
        ]);

        Language::create([
            'id'   => '9eaa5c43-db07-4597-ac8c-156253e84376',
            'name' => 'Kurdish',
            'locale' => 'ku'
        ]);

        Language::create([
            'id'   => '40aafb9e-41e2-11f0-8065-f75fd6b290d7',
            'name' => 'Arabic',
            'locale' => 'ar'
        ]);
        Language::create([
            'id'   => '488cb15e-50f6-11f0-8a5f-3b0d113e458b',
            'name' => 'French',
            'locale' => 'fr'
        ]);
        Language::create([
            'id'   => '306e04b6-50f5-11f0-a082-e3e55c5baecd',
            'name' => 'Syriac',
            'locale' => 'su'
        ]);
        Language::create([
            'id'   => '306e0826-50f5-11f0-a083-6b25e90008a2',
            'name' => 'Assyrian',
            'locale' => 'as'
        ]);
    }
}
