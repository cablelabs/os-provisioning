<?php

namespace Modules\ProvBase\Database\Seeders;

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;
use Modules\ProvBase\Entities\Modem;

class ModemTableSeeder extends \BaseSeeder {

	public function run()
	{
		$faker = Faker::create();

		foreach(range(1, $this->max_seed) as $index)
		{
			Modem::create([
				'mac' => $faker->macAddress(),
				'description' => $faker->realText(200),
				'network_access' => $faker->boolean(),
				'public' => (rand(0,100) < 5 ? 1 : 0),
				'serial_num' => $faker->sentence(),
				'inventar_num' => $faker->sentence(),
				'contract_id' => rand(1,500),
				'configfile_id' => rand(1,$this->max_seed_l2),
				'qos_id' => rand(1,$this->max_seed_l2),
				'tree_id' => rand(3,$this->max_seed_big),
				'status' => (rand(0,10) > 2 ? rand(300,620) : 0),
				'x' => 13 + $faker->longitude() / 10,
				'y' => 50 + $faker->latitude() / 10,
			]);
		}
	}

}