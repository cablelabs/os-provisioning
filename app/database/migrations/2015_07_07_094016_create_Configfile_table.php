<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateConfigfileTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('configfile', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name');
			$table->text('text');
			$table->enum('type', array('generic', 'network', 'vendor', 'user'));
			$table->enum('device', array('cm', 'mta'));
			$table->enum('public', array('yes', 'no'));
			$table->integer('parent_id')->unsigned();
			$table->string('firmware')->default("");
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('configfile');

		// remove all config and firmware files
		$files = array();
		$files['cm'] = glob('/tftpboot/cm/*');              // get all files in dir
		$files['mta'] = glob('/tftpboot/mta/*');              // get all files in dir
		$files['fw'] = glob('/tftpboot/fw/*');              // get all files in dir

		foreach ($files as $type) {
			foreach ($type as $file) {
			if(is_file($file))
				unlink($file);
			}
		}
	}

}
