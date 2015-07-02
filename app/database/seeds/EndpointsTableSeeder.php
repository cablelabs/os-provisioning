<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class EndpointsTableSeeder extends Seeder {

	public function run()
	{
		$faker = Faker::create();

		foreach(range(1, 100) as $index)
		{
			Endpoint::create([
				'mac' => (rand(0,1) == 1 ? $faker->macAddress() : ''),
				'description' => $faker->realText(200),
				'hostname' => $faker->colorName(),
				'public' => $faker->boolean(),
				'modem_id' => rand(0,30),
				'type' => (rand(0,1) == 1 ? 'cpe' : 'mta')
			]);
		}
	}

}