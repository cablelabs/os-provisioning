<?php

namespace Modules\ProvVoip\Database\Seeders;

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;
use Modules\ProvVoip\Entities\Mta;
use Modules\ProvBase\Entities\Configfile;
use Modules\ProvBase\Entities\Modem;

class MtaTableSeeder extends \BaseSeeder {

	public function run()
	{
		$faker = Faker::create();

		foreach(range(1, self::$max_seed) as $index)
		{
			Mta::create([
				'mac' => $faker->macAddress(),
				'type' => (rand(0, 1) == 1 ? 1 : 2),
				'modem_id' => Modem::all()->random(1)->id,
				'configfile_id' => Configfile::where('device', '=', 'mta')->get()->random(1)->id,
			]);
		}
	}

}
