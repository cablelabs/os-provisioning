<?php
/**
 * Copyright (c) NMS PRIME GmbH ("NMS PRIME Community Version")
 * and others – powered by CableLabs. All rights reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at:
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Modules\ProvVoip\Database\Seeders;

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;
use Modules\ProvVoip\Entities\PhonenumberManagement;

// don't forget to add Seeder in DatabaseSeeder.php
class PhonenumberManagementTableSeeder extends \BaseSeeder
{
    public function run()
    {
        $faker = Faker::create();

        foreach (range(1, self::$max_seed) as $index) {
            PhonenumberManagement::create([
                'phonenumber_id' => 300000 + $index,
                'activation_date' => $faker->dateTimeBetween('-2 years', '+1 year'),
                'porting_in' => rand(0, 1),
                'carrier_in' => '',
                'deactivation_date' => $faker->dateTimeBetween('now', '+1 year'),
                'porting_out' => 0,
                'carrier_out' => '',
                'subscriber_company' => (rand(0, 10) > 7 ? $faker->company : ''),
                'subscriber_department' => '',
                'subscriber_salutation' => rand(1, 4),
                'subscriber_academic_degree' => rand(1, 3),
                'subscriber_firstname' => $faker->firstName,
                'subscriber_lastname' => $faker->lastName,
                'subscriber_street' => $faker->streetName,
                'subscriber_house_number' => rand(1, 128),
                'subscriber_zip' => substr($faker->postcode, 0, 5),
                'subscriber_city' => $faker->city,
                'subscriber_country' => 1,
            ]);
        }
    }
}
