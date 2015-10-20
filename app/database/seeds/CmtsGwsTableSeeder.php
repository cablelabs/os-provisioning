<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;
use Models\CmtsGw;

class CmtsGwsTableSeeder extends Seeder {

	public function run()
	{
		$faker = Faker::create();

		foreach(range(1, 5) as $index)
		{
			CmtsGw::create([
				'hostname' => $faker->unique->state,
				'type' => "cmts",
				'ip' => $faker->ipv4(),
				'community_rw' => "private",
				'community_ro' => "public",
				'company' => str_random(10)
				// 'network'
				// 'state'
				// 'monitoring'

			]);
		}
	}

}