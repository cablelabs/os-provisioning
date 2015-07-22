<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;
use Models\Quality;

class QualitiesTableSeeder extends Seeder {

	public function run()
	{
		$faker = Faker::create();

		foreach(range(1, 10) as $index)
		{
			Quality::create([
				'name' => 'QOS-'.$index,
				'ds_rate_max' => rand(1,100),
				'us_rate_max' => rand(1,10)
			]);
		}
	}

}