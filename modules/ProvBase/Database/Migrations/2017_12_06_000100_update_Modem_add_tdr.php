<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateModemAddTdr extends BaseMigration {

	// name of the table to create
	protected $tablename = "modem";

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table($this->tablename, function(Blueprint $table)
		{
			$table->float('tdr');
		});

		return parent::up();
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
			$table->dropColumn('tdr');
		});
	}

}
