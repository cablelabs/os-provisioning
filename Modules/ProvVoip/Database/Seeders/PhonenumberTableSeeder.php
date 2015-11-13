<?php

namespace Modules\ProvVoip\Database\Seeders;

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;
use Modules\ProvVoip\Entities\Phonenumber;

# don't forget to add Seeder in DatabaseSeeder.php
class PhonenumberTableSeeder extends \Seeder {

	public function run()
	{
		foreach(range(0, 4) as $index)
		{
			Phonenumber::create([
				'prefix_number' => "03725",
				'number' => rand(100,999999),
				'mta_id' => 100000 + $index,
				'port' => 1,
				'active' => 1,
			]);
		}
	}

}
