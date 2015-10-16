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

		$this->call('ConfigfilesTableSeeder');
		$this->call('QualitiesTableSeeder');
		$this->call('ModemsTableSeeder');
		$this->call('EndpointsTableSeeder');
		$this->call('CmtsGwsTableSeeder');
		$this->call('IpPoolsTableSeeder');
		$this->call('MtasTableSeeder');

		// change owner of files that need to be editable for apache on updates
		system('/bin/chown -R apache /tftpboot/cm');
		system('/bin/chown -R apache /etc/dhcp/nms/cmts_gws');

	}

}
