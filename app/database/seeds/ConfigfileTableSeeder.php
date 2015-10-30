<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;
use Models\Configfile;

class ConfigfileTableSeeder extends Seeder {

	public function run()
	{
		$faker = Faker::create();

		foreach(range(1, 5) as $index)
		{
			Configfile::create([
				'name' => $faker->colorName(),
				'parent_id' => 0,
				'device' => (rand(0,100) > 30 ? 1 : 2),
				'text' => 'SnmpMibObject sysLocation.0 String "Test Lab" ;'
			]);
		}

		$firmware_dummies = array("fw_dummy1_v3.7.12.bin", "fw_dummy2_v1.7-fix12.bin");

		foreach ($firmware_dummies as $firmware_dummy) {
			touch("/tftpboot/fw/".$firmware_dummy);
		}
	}

}
