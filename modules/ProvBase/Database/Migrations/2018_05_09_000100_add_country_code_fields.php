<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCountryCodeFields {

	// name of the table to create
	protected $tablenames = [
		"contract",
		"modem",
	];

	/**
	 * Run the migrations.
	 * For using the envia TEL API we need some changes in storing the contracts data.
	 *
	 * @return void
	 */
	public function up()
	{
		foreach ($this->tablenames as $tablename) {
			Schema::table($tablename, function(Blueprint $table) {
				$table->string('country_code', 2)->after('country_id')->nullable()->default(NULL);
			});
		}

		$tablename = 'global_config';
		Schema::table("global_config", function(Blueprint $table) {
			$table->string('default_country_code', 2)->after('headline2');
		});

		DB::update("UPDATE $tablename SET default_country_code='DE'");
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		foreach ($this->tablenames as $tablename) {
			Schema::table($tablename, function(Blueprint $table) {
				$table->dropColumn([
					'country_code',
				]);
			});
		}

		$tablename = 'global_config';
		Schema::table("global_config", function(Blueprint $table) {
				$table->dropColumn([
					'default_country_code',
				]);
		});

	}

}
