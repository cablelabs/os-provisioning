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

		// change owner of files that need to be editable for apache on updates
		system('/bin/chown -R apache /tftpboot/cm');
		system('/bin/chown -R apache /tftpboot/mta');
		system('/bin/chown -R apache /etc/dhcp/');
	}

}
