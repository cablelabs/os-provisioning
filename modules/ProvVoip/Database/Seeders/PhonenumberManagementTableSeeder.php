<?php

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
                'deactivation_date' => (rand(0, 10) > 7 ? $faker->dateTimeBetween('now', '+1 year') : '0000-00-00'),
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
