<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateContractAddEmailcount extends BaseMigration {

	protected $tablename = 'contract';

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table($this->tablename, function(Blueprint $table)
		{
			$table->integer('emailcount')->unsigned();
		});

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table($this->tablename, function(Blueprint $table)
		{
			$table->dropColumn([
				'emailcount',
			]);
		});
	}

}
