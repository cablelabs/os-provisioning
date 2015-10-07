<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;
use Models\Mta;

class MtasTableSeeder extends Seeder {

	public function run()
	{
		$faker = Faker::create();

		foreach(range(1, 10) as $index)
		{
			Mta::create([
				'mac' => $faker->macAddress(),
				'type' => (rand(0, 1) == 1 ? 'sip' : 'packetcable'),
				'configfile_id' => rand(1,10),
				'modem_id' => (100000 + $index),
				'hostname' => "mta-".(100000 + $index),
			]);
		}
	}

}
