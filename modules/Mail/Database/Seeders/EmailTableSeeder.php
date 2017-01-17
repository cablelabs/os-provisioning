<?php

namespace Modules\Mail\Database\Seeders;

use Faker\Factory as Faker;
use Modules\Mail\Entities\Email;
use Modules\ProvBase\Entities\Domain;
use Modules\ProvBase\Entities\Contract;

class EmailTableSeeder extends \BaseSeeder {

	public function run()
	{
		$faker = Faker::create();

		foreach(range(1, $this->max_seed) as $index)
		{
			$contract_id = Contract::all()->random(1)->id;

			Email::create([
				'contract_id' => $contract_id,
				'domain_id' => Domain::where('type', '=', 'email')->get()->random(1)->id,
				'localpart' => $faker->userName(),
				'password' => $faker->password(),
				'index' => rand(1, Contract::find($contract_id)->emailcount),
				'greylisting' => rand(0,1),
				'blacklisting' => rand(0,1),
				'forwardto' => $faker->email(),
			]);
		}
	}

}
