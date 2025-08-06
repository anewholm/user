<?php namespace Acorn\User\Updates;

use Acorn\User\Models\Ethnicity;
use Winter\Storm\Database\Updates\Seeder;

class SeedUserEthnicities extends Seeder
{
    public function run()
    {
        // Common
        $ethnicity = Ethnicity::create([
            'id'   => '1072eade-528d-11f0-95ea-432029354c94',
            'name' => 'Arab',
        ]);
        $ethnicity->setAttributeTranslated('name', 'عربي', 'ar');
        $ethnicity->setAttributeTranslated('name', 'Erab', 'ku');

        $ethnicity = Ethnicity::create([
            'id'   => '1072ed2c-528d-11f0-95eb-9be8c0b88e94',
            'name' => 'Kurd',
        ]);
        $ethnicity->setAttributeTranslated('name', 'عربي', 'ar');
        $ethnicity->setAttributeTranslated('name', 'Kurd', 'ku');

        // Less Common
        $ethnicity = Ethnicity::create([
            'id'   => '1072ed2c-528d-11f0-95eb-9be8c0b88e94',
            'name' => 'Turkmen',
        ]);
        $ethnicity->setAttributeTranslated('name', 'التركمان', 'ar');
        $ethnicity->setAttributeTranslated('name', 'Tirkmenî', 'ku');

        $ethnicity = Ethnicity::create([
            'id'   => '1072ed2c-528d-11f0-95eb-9be8c0b88e94',
            'name' => 'Syriac',
        ]);
        $ethnicity->setAttributeTranslated('name', 'السريانية', 'ar');
        $ethnicity->setAttributeTranslated('name', 'Suryanî', 'ku');

        $ethnicity = Ethnicity::create([
            'id'   => '1072ed2c-528d-11f0-95eb-9be8c0b88e94',
            'name' => 'Armenian',
        ]);
        $ethnicity->setAttributeTranslated('name', 'الأرمنية', 'ar');
        $ethnicity->setAttributeTranslated('name', 'Ermenî', 'ku');

        // Other
        $ethnicity = Ethnicity::create([
            'id'   => '1072edfe-528d-11f0-95ed-a7fb340d81de',
            'name' => 'Other',
        ]);
        $ethnicity->setAttributeTranslated('name', 'ا', 'ar');
        $ethnicity->setAttributeTranslated('name', 'A dîn', 'ku');
    }
}
