<?php

namespace Modules\ProvBase\Database\Seeders;

use Faker\Factory as Faker;
use Modules\ProvBase\Entities\Domain;

class DomainTableSeeder extends \BaseSeeder {

	public function run()
	{
		$faker = Faker::create();

		foreach(range(1, self::$max_seed) as $index)
		{
			$alias = $faker->domainWord();
			foreach(range(1, rand(1,10)) as $idx)
				$alias .= ':'.$faker->domainWord();

			$type = Domain::getPossibleEnumValues('type');

			Domain::create([
				'name' => $faker->domainName(),
				'alias' => $alias,
				'type' => $type[array_rand($type)],
			]);
		}
	}

}
