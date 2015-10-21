<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;
use Models\Endpoint;

class EndpointTableSeeder extends Seeder {

	public function run()
	{
		$faker = Faker::create();

		foreach(range(1, 10) as $index)
		{
			Endpoint::create([
				'mac' => (rand(0,1) == 1 ? $faker->macAddress() : ''),
				'description' => $faker->realText(200),
				'type' => (rand(0,1) == 1 ? 'cpe' : 'mta')
			]);
		}
	}

}