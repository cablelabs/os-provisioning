<?php

namespace Modules\ProvBase\Database\Seeders;

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;
use Modules\ProvBase\Entities\Cmts;

class CmtsTableSeeder extends \BaseSeeder {

	public function run()
	{
		$faker = Faker::create();
		$company = ['Cisco', 'Casa'];

		foreach(range(1, $this->max_seed_l2) as $index)
		{
			Cmts::create([
				'hostname' => $faker->unique->name,
				'type' => "cmts",
				'ip' => $faker->ipv4(),
				'community_rw' => "private",
				'community_ro' => "public",
				'company' => $company[array_rand($company)],
				// 'network'
				// 'state'
				// 'monitoring'

			]);
		}
	}

}