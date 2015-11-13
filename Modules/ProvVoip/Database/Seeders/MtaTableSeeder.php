<?php

namespace Modules\ProvVoip\Database\Seeders;

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;
use Modules\ProvVoip\Entities\Mta;


class MtaTableSeeder extends \Seeder {

	public function run()
	{
		$faker = Faker::create();

		foreach(range(1, 2) as $index)
		{
			Mta::create([
				'mac' => $faker->macAddress(),
				'type' => (rand(0, 1) == 1 ? 1 : 2),
				'modem_id' => (100000 + $index - 1),
			]);
		}
	}

}
