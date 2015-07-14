<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;
use Models\Quality;

class QualitiesTableSeeder extends Seeder {

	public function run()
	{
		$faker = Faker::create();
		$i = 0;

		foreach(range(1, 10) as $index)
		{
			Quality::create([
				'name' => 'QOS-'.$i,
				'ds_rate_max' => rand(0,100000000),
				'us_rate_max' => rand(0,10000000)
			]);
			$i++;
		}
	}

}