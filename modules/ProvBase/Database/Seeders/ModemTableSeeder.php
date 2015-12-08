<?php

namespace Modules\ProvBase\Database\Seeders;

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;
use Modules\ProvBase\Entities\Modem;
use Modules\ProvBase\Entities\Contract;
use Modules\ProvBase\Entities\Configfile;
use Modules\ProvBase\Entities\Qos;

class ModemTableSeeder extends \BaseSeeder {

	public function run()
	{
		$faker = Faker::create();

		foreach(range(1, $this->max_seed) as $index)
		{
			$contract    = Contract::all()->random(1);
			$contract_id = $contract->id;

			$firstname = $contract->firstname;
			$lastname  = $contract->lastname;
			$zip       = $contract->zip;
			$city      = $contract->city;
			$street    = $contract->street;
			$x         = $contract->x;
			$y         = $contract->y;
			$country_id= $contract->country_id;

			if (rand(0,10) > 8)
			{
				$firstname = $faker->firstName;
				$lastname  = $faker->lastName;
				$zip       = $faker->postcode;
				$city      = $faker->city;
				$street    = $faker->streetName;
				$x         = 13 + $faker->longitude() / 10;
				$y         = 50 + $faker->latitude() / 10;
				$country_id= 0;
			}

			if (\Module::find('HfcBase')->active())
			{
				if (\Modules\HfcBase\Entities\Tree::all()->count() > 2)
					$tree_id = \Modules\HfcBase\Entities\Tree::where('id', '>', '2')->get()->random(1)->id;
				else
					$tree_id = rand(3, $this->max_seed);
			}

			Modem::create([
				'mac' => $faker->macAddress(),
				'description' => $faker->realText(200),
				'network_access' => $faker->boolean(),
				'public' => (rand(0,100) < 5 ? 1 : 0),
				'serial_num' => $faker->sentence(),
				'inventar_num' => $faker->sentence(),
				'contract_id' => $contract_id,
				'configfile_id' => Configfile::all()->random(1)->id,
				'qos_id' => Qos::all()->random()->id,
				'tree_id' => $tree_id,
				'status' => (rand(0,10) > 2 ? rand(300,620) : 0),
				'firstname' => $firstname,
				'lastname' => $lastname,
				'zip' => $zip,
				'city' => $city,
				'country_id' => $country_id,
				'street' => $street,
				'x' => $x,
				'y' => $y,
			]);

		}
	}

}