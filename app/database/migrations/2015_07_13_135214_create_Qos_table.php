<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateQosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('qos', function(Blueprint $table)
		{
			$table->increments('id');
			$table->float('ds_rate_max');
			$table->float('us_rate_max');
			$table->integer('ds_rate_max_help')->unsigned();
			$table->integer('us_rate_max_help')->unsigned();
			$table->string('name');
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
		Schema::drop('qos');
	}

}
