<?php

namespace Modules\ProvBase\Database\Seeders;

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;
use Modules\ProvBase\Entities\Qos;

class QosTableSeeder extends \BaseSeeder {

	public function run()
	{
		foreach(range(1, $this->max_seed_l2) as $index)
		{
			Qos::create([
				'name' => 'QOS-'.$index,
				'ds_rate_max' => rand(1,100),
				'us_rate_max' => rand(1,10)
			]);
		}
	}

}