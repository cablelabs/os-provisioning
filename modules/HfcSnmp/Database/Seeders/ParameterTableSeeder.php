<?php

namespace Modules\HfcSnmp\Database\Seeders;

use Faker\Factory as Faker;
use Modules\HfcSnmp\Entities\Parameter;

class ParameterTableSeeder extends \BaseSeeder
{
    public function run()
    {
        $faker = Faker::create();

        foreach (range(1, 4) as $index) {
            foreach (range(1, rand(2, 10)) as $id) {
                Parameter::create([
                    'netelementtype_id' => $index,
                    'oid_id' 			=> rand(1, 50),
                    'html_frame' 		=> rand(1, 10) > 5 ? rand(1, 9) : rand(11, 89),
                    'html_id' 			=> rand(1, 6),
                ]);
            }
        }
    }
}
