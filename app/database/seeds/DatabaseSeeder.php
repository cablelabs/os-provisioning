<?php

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();

/*		$this->call('ConfigfilesTableSeeder');
		$this->call('QualitiesTableSeeder');
		
		$this->call('ModemsTableSeeder');
		$this->call('EndpointsTableSeeder');*/

		$this->call('CmtsGwsTableSeeder');
	}

}
