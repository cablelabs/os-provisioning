<?php

namespace Modules\ProvVoip\Database\Seeders;

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;
use Modules\ProvVoip\Entities\Mta;


class MtaTableSeeder extends \BaseSeeder {

	public function run()
	{
		$faker = Faker::create();

		foreach(range(1, $this->max_seed) as $index)
		{
			Mta::create([
				'mac' => $faker->macAddress(),
				'type' => (rand(0, 1) == 1 ? 1 : 2),
				'modem_id' => rand (100000, 100000 + $this->max_seed),
			]);
		}
	}

}
