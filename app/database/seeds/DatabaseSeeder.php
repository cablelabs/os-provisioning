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

		$this->call('ConfigfileTableSeeder');
		$this->call('QosTableSeeder');
		$this->call('ModemTableSeeder');
		$this->call('EndpointTableSeeder');
		$this->call('CmtsTableSeeder');
		$this->call('IpPoolTableSeeder');
		$this->call('PhonenumberTableSeeder');
		$this->call('MtaTableSeeder');

	}

}
