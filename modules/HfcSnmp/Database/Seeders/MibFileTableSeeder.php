<?php

namespace Modules\HfcSnmp\Database\Seeders;

use Faker\Factory as Faker;
use Modules\HfcSnmp\Entities\MibFile;

class MibFileTableSeeder extends \BaseSeeder
{
    public function run()
    {
        $faker = Faker::create();

        foreach (range(1, 5) as $index) {
            $name = 'DOCS-FAKE-'.$faker->colorName.'-MIB';
            $vers = $faker->year.$faker->month.$faker->dayOfMonth.'0000Z';

            MibFile::create([
                'name' => $name,
                'version' => $vers,
                'filename' => $name.'_'.$vers,
            ]);
        }
    }
}
