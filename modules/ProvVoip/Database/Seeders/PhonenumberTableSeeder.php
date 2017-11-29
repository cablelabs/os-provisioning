<?php

namespace Modules\ProvVoip\Database\Seeders;

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;
use Modules\ProvVoip\Entities\Phonenumber;
use Modules\ProvVoip\Entities\Mta;
use Modules\ProvBase\Entities\Modem;


// don't forget to add Seeder in DatabaseSeeder.php
class PhonenumberTableSeeder extends \BaseSeeder {

	public function run()
	{
		foreach(range(0, self::$max_seed) as $index)
		{
			Phonenumber::create([
				'prefix_number' => "03725",
				'number' => rand(100,999999),
				'mta_id' => Mta::all()->random(1)->id,
				'port' => 1,
				'active' => 1,
			]);
		}
	}

}
