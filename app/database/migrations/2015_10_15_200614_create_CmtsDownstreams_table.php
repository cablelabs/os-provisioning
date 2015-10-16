<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCmtsDownstreamsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cmts_downstreams', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('cmts_gws_id')->unsigned();
			$table->integer('index')->unsigned();
			$table->string('alias');
			$table->string('description');
			$table->integer('frequency')->unsigned();
			$table->enum('modulation', ['qam64','qam256']);
			$table->integer('power');
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
		Schema::drop('cmts_downstreams');
	}

}
