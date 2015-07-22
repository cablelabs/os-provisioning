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
			$table->string('name');
			$table->string('mac',17);
			$table->text('description');
			$table->enum('type', array('cpe','mta'));
			$table->boolean('public');
			// $table->integer('modem_id')->unsigned(); // depracted
			$table->timestamps();
		});

		DB::update("ALTER TABLE endpoints AUTO_INCREMENT = 200000;");
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
