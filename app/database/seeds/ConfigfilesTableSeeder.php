<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;
use Models\Configfile;

class ConfigfilesTableSeeder extends Seeder {

	public function run()
	{
		$faker = Faker::create();

		foreach(range(1, 10) as $index)
		{
			Configfile::create([
				'name' => $faker->colorName(),
				'parent_id' => 0,
				'device' => (rand(0,100) > 30 ? 1 : 2),
				'text' => 'SnmpMibObject sysLocation.0 String "Test Lab" ;'
			]);
		}
	}

}
