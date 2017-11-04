<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateProvBaseAddMaxCpe{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('provbase', function(Blueprint $table)
		{
			$table->smallInteger('max_cpe')->nullable();
		});

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('provbase', function(Blueprint $table)
		{
			$table->dropColumn(['max_cpe']);
		});
	}

}
