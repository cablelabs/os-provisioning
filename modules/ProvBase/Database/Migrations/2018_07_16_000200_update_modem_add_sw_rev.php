<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*
 * MPR: Modem Positioning Rule
 */
class UpdateModemAddSwRev extends BaseMigration {

	protected $tablename = 'modem';

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table($this->tablename, function(Blueprint $table) {
			$table->string('sw_rev')->nullable()->default(NULL);
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
		Schema::table($this->tablename, function(Blueprint $table) {
			$table->dropColumn('sw_rev');
		});
	}

}
