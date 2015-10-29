<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateModemTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$dir = '/tftpboot/cm';
		if(!is_dir($dir))
			mkdir ($dir, '0755');
		
		Schema::create('modem', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name');
			$table->string('hostname');
			$table->integer('contract_id')->unsigned();
			$table->string('mac')->sizeof(17);
			$table->integer('status');
			$table->boolean('public');
			$table->boolean('network_access');
			$table->string('serial_num');
			$table->string('inventar_num');
			$table->text('description');
			$table->integer('parent');
			$table->integer('configfile_id')->unsigned();
			$table->integer('qos_id')->unsigned();
			$table->timestamps();
		});

		DB::update("ALTER TABLE modem AUTO_INCREMENT = 100000;");
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('modem');
	}

}
