<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

use Models\Cmts;

class CreateCmtsTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cmts', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('hostname');
			$table->string('type');
			$table->string('ip');		// bundle ip
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
		$c = Cmts::first();
		if ($c) $c->del_cmts_includes();

		Schema::drop('cmts');
	}

}
