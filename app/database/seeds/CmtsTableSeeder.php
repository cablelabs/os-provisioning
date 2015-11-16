<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;
use Models\Cmts;

class CmtsTableSeeder extends BaseSeeder {

	public function run()
	{
		$faker = Faker::create();

		foreach(range(1, $this->max_seed_l2) as $index)
		{
			Cmts::create([
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