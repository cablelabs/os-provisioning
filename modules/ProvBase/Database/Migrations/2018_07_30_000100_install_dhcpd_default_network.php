<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class InstallDhcpdDefaultNetwork extends BaseMigration {

	protected $tablename = '';

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// regenerate dhcpd files as we installed a new default network config
		\Artisan::call('nms:dhcp');
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
	}

}
