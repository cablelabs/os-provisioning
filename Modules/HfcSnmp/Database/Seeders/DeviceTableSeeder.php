<?php

namespace Modules\HfcSnmp\Database\Seeders;

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;
use Modules\HfcSnmp\Entities\Device;

class DeviceTableSeeder extends \BaseSeeder {

	public function run()
	{
		$faker = Faker::create();

		foreach(range(1, $this->max_seed) as $index)
		{
			Device::create([
				'name' => $faker->unique->state,
				'ip' => $faker->ipv4,
				'community_ro' => 'public',
				'community_rw' => 'private',
			]);
		}
	}

}