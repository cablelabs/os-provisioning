<?php 

namespace Modules\HfcSnmp\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;


class HfcSnmpTableSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Model::unguard();
		
		$this->call("Modules\HfcSnmp\Database\Seeders\DeviceTableSeeder");
		$this->call("Modules\HfcSnmp\Database\Seeders\DeviceTypeTableSeeder");
		$this->call("Modules\HfcSnmp\Database\Seeders\SnmpMibTableSeeder");
	}

}
