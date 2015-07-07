<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEndpointsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('endpoints', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('hostname');
			$table->string('mac',17);
			$table->text('description');
			$table->enum('type', array('cpe','mta'));
			$table->boolean('public');
			$table->integer('modem_id')->unsigned();
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
		Schema::drop('endpoints');
	}

}
