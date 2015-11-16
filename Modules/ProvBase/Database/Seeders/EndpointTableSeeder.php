<?php

namespace Modules\ProvBase\Database\Seeders;

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;
use Modules\ProvBase\Entities\Endpoint;

class EndpointTableSeeder extends \BaseSeeder {

	public function run()
	{
		$faker = Faker::create();

		foreach(range(1, $this->max_seed_l2) as $index)
		{
			Endpoint::create([
				'mac' => (rand(0,1) == 1 ? $faker->macAddress() : ''),
				'description' => $faker->realText(200),
				'type' => (rand(0,1) == 1 ? 'cpe' : 'mta')
			]);
		}
	}

}