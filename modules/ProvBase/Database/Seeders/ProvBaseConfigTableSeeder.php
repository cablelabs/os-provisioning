<?php

namespace Modules\ProvBase\Database\Seeders;

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;
use Modules\ProvBase\Entities\ProvBase;

class ProvBaseConfigTableSeeder extends \BaseSeeder {

	public function run()
	{
		foreach(range(1, $this->max_seed_l2) as $index)
		{
			$faker = Faker::create();
			ProvBase::create([
				'provisioning_server' => $faker->ipv4(),
			]);
		}
	}

}