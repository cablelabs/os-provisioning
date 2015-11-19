<?php

namespace Modules\HfcBase\Database\Seeders;

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;
use Modules\HfcBase\Entities\Tree;

class TreeTableSeeder extends \BaseSeeder {

	/*
	 * TODO: These "helper" functions should be moved to Model context
	 */
	private function type ($t)
	{
		switch ($t) {
			case '1': return 'NET';
			case '2': return 'CMTS';
			case '3': return 'DATA';
			case '4': return 'CMTS';
			case '5': return 'NODE';
			case '6': return 'AMP';
			
			default:
				return 'AMP';
		}
	}

	private function state ($s)
	{
		switch ($s) {
			case '0': return 'OK';
			case '1': return 'YELLOW';
			case '2': return 'RED';
			
			default:
				return 'OK';
		}
	}


	public function run()
	{
		$faker = Faker::create();

		foreach(range(1, $this->max_seed_big) as $index)
		{
			Tree::create([
				'name' => $faker->domainWord(),
				'ip' => $faker->ipv4(),
				'type' => $this->type(rand(1,20)),
				'state' => $this->state(rand(0,10)),
				'parent' => rand (1,$this->max_seed),
				'descr' => $faker->sentence(),
				'pos' => $faker->latitude().','.$faker->longitude(),
				'link' => url()
			]);
		}
	}

}