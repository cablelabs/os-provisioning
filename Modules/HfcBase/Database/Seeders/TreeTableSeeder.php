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
			case '4': return 'CLUSTER';
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


	private function pos_dumping($trees)
	{
		foreach ($trees as $tree) 
		{
			$children = $tree->get_children();
			$this->pos_dumping($children);

			if (isset($pos) && rand(0,10) > 7)
			{
				$tree->pos = $pos;
				$tree->save();

				echo "\r\n change pos of tree with id ".$tree->id;
			}

			$pos = $tree->pos;
		}
	}


	public function run()
	{
		$faker = Faker::create();
		$i = 2;

		foreach(range(1, $this->max_seed_big) as $index)
		{
			$x = 13 + $faker->longitude() / 10;
			$y = 50 + $faker->latitude() / 10;

			Tree::create([
				'name' => $faker->domainWord(),
				'ip' => $faker->ipv4(),
				'type' => $this->type(rand(1,20)),
				'state' => $this->state(rand(0,10)),
				'parent' => rand (2,$i++),
				'descr' => $faker->sentence(),
				'pos' => $x.','.$y,
				'link' => url()
			]);
		}

		$root = Tree::find(2);

		// Make top level elements of type NET, second level of type CLUSTER
		foreach ($root->get_children() as $net) 
		{
			$net->type = 'NET';
			$net->save();

			foreach ($net->get_children() as $cluster) 
			{
				$cluster->type = 'CLUSTER';
				$cluster->save();
			}
		}

		$this->pos_dumping (Tree::where('type', '=', 'NET')->get());

		Tree::relation_index_build_all(1);
	}

}