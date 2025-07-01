<?php namespace Acorn\User\Updates;

use Acorn\User\Models\Religion;
use Winter\Storm\Database\Updates\Seeder;

class SeedUserReligions extends Seeder
{
    public function run()
    {
        $religion = Religion::create([
            'id'   => '1072eade-528d-11f0-95ea-432029354c94',
            'name' => 'Christianity',
        ]);
        $religion->setAttributeTranslated('name', 'المسيحية', 'ar');
        $religion->setAttributeTranslated('name', 'Mesahî', 'ku');

        $religion = Religion::create([
            'id'   => '1072ed2c-528d-11f0-95eb-9be8c0b88e94',
            'name' => 'Islam',
        ]);
        $religion->setAttributeTranslated('name', 'الإسلام', 'ar');
        $religion->setAttributeTranslated('name', 'Îzlam', 'ku');

        $religion = Religion::create([
            'id'   => '1072eda4-528d-11f0-95ec-d32bc2569c13',
            'name' => 'Buddhism',
        ]);
        $religion->setAttributeTranslated('name', 'البوذية', 'ar');
        $religion->setAttributeTranslated('name', 'Budîzim', 'ku');
        
        $religion = Religion::create([
            'id'   => '1072edfe-528d-11f0-95ed-a7fb340d81de',
            'name' => 'Other',
        ]);
        $religion->setAttributeTranslated('name', 'ا', 'ar');
        $religion->setAttributeTranslated('name', 'A dîn', 'ku');
    }
}
