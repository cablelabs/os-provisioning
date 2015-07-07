<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class EndpointsTableSeeder extends Seeder {

	public function run()
	{
		$faker = Faker::create();

/*
		foreach(range(1, 10000) as $index)
		{
			Endpoint::create([
				'mac' => (rand(0,1) == 1 ? $faker->macAddress() : ''),
				'description' => $faker->realText(200),
				'hostname' => $faker->colorName(),
				'public' => (rand(0,100) < 5 ? 1 : 0),
				'modem_id' => rand(100000,104999),
				'type' => (rand(0,1) == 1 ? 'cpe' : 'mta')
			]);
		}
*/
	}

}