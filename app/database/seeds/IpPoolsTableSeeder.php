<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class IpPoolsTableSeeder extends Seeder {

	public function run()
	{
		$faker = Faker::create();

		foreach(range(1, 4) as $index)
		{
			IpPool::create([
				'cmts_gw_id' => 1,
				'type' => "CM",
				'ip_pool_start' => $faker->ipv4(),
				'ip_pool_end' => $faker->ipv4(),
				'router_ip' => $faker->ipv4(),
			]);
		}
	}

}