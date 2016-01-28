<?php

namespace Modules\HfcSnmp\Database\Seeders;

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;
use Modules\HfcSnmp\Entities\SnmpMib;

class SnmpMibTableSeeder extends \BaseSeeder {

	public function run()
	{
		$faker = Faker::create();

		foreach(range(1, $this->max_seed) as $index)
		{
			SnmpMib::create([
				'field' => $faker->name,
			]);
		}
	}

}