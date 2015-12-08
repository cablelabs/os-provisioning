<?php 

namespace Modules\Hfcbase\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class HfcbaseTableSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Model::unguard();
		
		$this->call("Modules\HfcBase\Database\Seeders\TreeTableSeeder");
	}

}
