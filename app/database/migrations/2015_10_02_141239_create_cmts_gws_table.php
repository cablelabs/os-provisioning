<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

use Models\CmtsGw;

class CreateCmtsGwsTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cmts_gws', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('hostname');
			$table->string('type');
			$table->string('ip');		// bundle ip
			$table->string('community_rw');
			$table->string('community_ro');
			$table->string('company');
			$table->integer('network');
			$table->integer('state');
			$table->integer('monitoring');
			$table->timestamps();		// created_at and updated_at
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		$c = CmtsGw::first();
		$c->del_cmts_includes();

		Schema::drop('cmts_gws');

		// remove all through dhcpCommand created cmts config files
		$files = glob('/etc/dhcp/nms/cmts_gws/*');              // get all files in dir
		foreach ($files as $file) 
		{
			if(is_file($file))
			unlink($file);                                                  // delete file
		}


	}

}
