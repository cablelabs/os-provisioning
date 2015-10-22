<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDeviceTypeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create(strtolower('DeviceType'), function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name');
			$table->string('vendor');
			$table->string('version');
			$table->text('description');
			$table->integer('parent_id')->unsigned();
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
		Schema::drop('DeviceType');
	}

}
