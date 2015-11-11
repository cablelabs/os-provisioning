<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSnmpValueTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create(strtolower('SnmpValue'), function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('device_id')->unsigned();
			$table->integer('snmpmib_id')->unsigned();
			$table->string('oid_index');
			$table->string('value');
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
		Schema::drop(strtolower('SnmpValue'));
	}

}
