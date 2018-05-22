<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGeocodeSourceField {

	// name of the table to create
	protected $tablename = "modem";

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {

		Schema::table($this->tablename, function(Blueprint $table) {
			$table->string('geocode_source')->after('y')->nullable()->default(NULL);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {

		Schema::table($this->tablename, function(Blueprint $table) {
			$table->dropColumn('geocode_source');
		});
	}

}
