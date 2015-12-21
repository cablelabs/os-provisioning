<?php

namespace Modules\ProvVoip\Database\Seeders;

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;
use Modules\ProvVoip\Entities\PhonenumberManagement;

# don't forget to add Seeder in DatabaseSeeder.php
class PhonenumberManagementTableSeeder extends \BaseSeeder {

	public function run()
	{
		foreach(range(0, 3) as $index)
		{
			PhonenumberManagement::create([
				'mta_id' => 300000 + $index,
				'subscriber_firstname' => $faker->firstName,
				'subscriber_lastname' => $faker->lastName,
				'subscriber_salutation' => rand(0, 3),
				'subscriber_academic_degree' => rand(0, 3),
				'subscriber_company' => (rand(0,10) > 7 ? $faker->company: ''),
				'subscriber_street' => $faker->streetName,
				'subscriber_house_number' => rand(1, 128),
				'subscriber_city' => $faker->city,
				'subscriber_zip' => $faker->postcode,
				'subscriber_country_id' => 0,
			]);
		}
	}
}
