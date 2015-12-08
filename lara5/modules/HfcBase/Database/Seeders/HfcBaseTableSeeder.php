<?php 

namespace Modules\HfcBase\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class HfcBaseTableSeeder extends Seeder {

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
