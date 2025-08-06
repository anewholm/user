<?php namespace Acorn\User\Updates;

use Acorn\User\Models\Religion;
use Winter\Storm\Database\Updates\Seeder;

class SeedUserReligions extends Seeder
{
    public function run()
    {
        // Common
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

        // Less common
        $religion = Religion::create([
            'id'   => '15d6c121-b2e9-47c2-965a-72d26db6544f',
            'name' => 'Ismailism',
        ]);
        $religion->setAttributeTranslated('name', 'الإسماعيلية', 'ar');
        $religion->setAttributeTranslated('name', 'Îsmaîlîzm', 'ku');

        $religion = Religion::create([
            'id'   => 'a1b360ad-0797-49d2-8059-406b79176cc1',
            'name' => 'Yazidism',
        ]);    
        $religion->setAttributeTranslated('name', 'اليزيدية', 'ar');
        $religion->setAttributeTranslated('name', 'Êzîdîtî', 'ku');

        $religion = Religion::create([
            'id'   => 'ab8afc59-d67c-4142-9b32-63f40ed05938',
            'name' => 'Druze',
        ]);
        $religion->setAttributeTranslated('name', 'درزي', 'ar');
        $religion->setAttributeTranslated('name', 'Druz', 'ku');

        $religion = Religion::create([
            'id'   => '044a9017-fedf-4e8c-9c84-5c27e3f77cbb',
            'name' => 'Judaism',
        ]);
        $religion->setAttributeTranslated('name', 'اليهودية', 'ar');
        $religion->setAttributeTranslated('name', 'Cihûtî', 'ku');

        // Non-standard
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
