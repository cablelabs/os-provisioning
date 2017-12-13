<?php

namespace Modules\ProvVoip\Database\Seeders;

use Modules\ProvVoip\Entities\Phonenumber;
use Modules\ProvVoip\Entities\Mta;


// don't forget to add Seeder in DatabaseSeeder.php
class PhonenumberTableSeeder extends \BaseSeeder {

	public function run()
	{
		foreach(range(1, self::$max_seed) as $index)
		{
			Phonenumber::create(static::get_fake_data('seed'));
		}
	}


	/**
	 * Returns an array with faked phonenumber data; used e.g. in seeding and testing
	 *
	 * @param $topic Context the method is used in (seed|test)
	 * @param $mta mta to create the phonenumber at; used in testing
	 *
	 * @author Patrick Reichel
	 */
	public static function get_fake_data($topic, $mta=null) {

		$faker =& \NmsFaker::getInstance();

		// in seeding mode: choose random mta to create phonenumber at
		if ($topic == 'seed') {
			$mta = Mta::all()->random(1);
			$mta_id = $mta->id;
		}
		else {
			if (!is_null($mta)) {
				$mta_id = $mta->id;
			}
			else {
				$mta_id = null;
			}
		}

		$ret = [
			'prefix_number' => "0".rand(2, 9).rand(0, 9999),
			'number' => rand(100,999999),
			'mta_id' => Mta::all()->random(1)->id,
			'port' => 1,
			'active' => rand(0, 1),
		];

		return $ret;
	}

}
