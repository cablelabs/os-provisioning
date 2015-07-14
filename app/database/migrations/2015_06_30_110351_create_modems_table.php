<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateModemsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('modems', function(Blueprint $table)
		{
			$table->increments('id');
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
			$table->integer('quality_id')->unsigned();
			$table->timestamps();
		});

		DB::update("ALTER TABLE modems AUTO_INCREMENT = 100000;");
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('modems');
	}

}
