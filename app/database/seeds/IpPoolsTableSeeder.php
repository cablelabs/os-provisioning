<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;
use Models\IpPool;

class IpPoolsTableSeeder extends Seeder {

	public function run()
	{
		$faker = Faker::create();
		$m = 0;
		$n = 0;

		foreach(range(1, 10) as $index)
		{
			IpPool::create([
				'cmts_gw_id' => rand(1,5),
				'type' => rand(0,4),
				'net' => '10.'.$m.'.'.$n.'.0',
				'netmask' => '255.255.255.0',
				'ip_pool_start' => '10.'.$m.'.'.$n.'.2',
				'ip_pool_end' => '10.'.$m.'.'.$n.'.253',
				'router_ip' => '10.'.$m.'.'.$n.'.1',		// = cmts ip
				'broadcast_ip' => '10.'.$m.'.'.$n.'.255',
				'dns1_ip' => '10.'.$m.'.'.$n.'.1',
			]);
			$m += 10;
		}
	}

}