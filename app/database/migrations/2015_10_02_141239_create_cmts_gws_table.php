<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

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
			$table->string('startIp');
			$table->string('endIp');
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
		Schema::drop('cmts_gws');
	}

}
