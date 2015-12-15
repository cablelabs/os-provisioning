<?php

namespace Modules\ProvBase\Database\Seeders;

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;
use Modules\ProvBase\Entities\ProvBase;

class ProvBaseConfigTableSeeder extends \BaseSeeder {

	public function run()
	{
		$faker = Faker::create();
		ProvBase::create([
			'provisioning_server' => $faker->ipv4,
			'ro_community' => 'public',
			'rw_community' => 'private',
			'notif_mail' => $faker->email,
			'domain_name' => 'seeder.domain.com',
			'startid_contract' => 500000,
			'startid_modem' => 100000,
			'startid_endpoint' => 200000,
		]);
	}

}