<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class GlobalConfigTableSeeder extends \BaseSeeder
{
    public function run()
    {
        $faker = Faker::create();
        GlobalConfig::create([
            'name' => $faker->name,
            'street' => $faker->streetName,
            'city' => $faker->city,
            'phone' => $faker->phonenumber,
            'mail' => $faker->email,
            'log_level' => 1,
            'headline1' => 'Das Monster',
            'headline2' => 'Schlägt zu',
        ]);
    }
}
