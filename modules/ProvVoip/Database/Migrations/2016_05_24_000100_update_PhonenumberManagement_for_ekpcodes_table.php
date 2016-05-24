<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Updater to add envia related data to contract
 *
 * @author Patrick Reichel
 */
class UpdatePhonenumberManagementForEkpCodesTable extends BaseMigration {

	// name of the table to create
	protected $tablename = "phonenumbermanagement";


    /**
	 * Run the migrations.
	 * For using the Envia API we need some changes in storing the contracts data.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->tablename, function(Blueprint $table) {

			$table->integer('carrier_in')->change();
			$table->integer('carrier_out')->change();

			$table->integer('ekp_in')->after('carrier_in');
			$table->integer('ekp_out')->after('carrier_out');
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
				'ekp_in',
				'ekp_out',
			]);

			$table->string('carrier_in', 16)->change();
			$table->string('carrier_out', 16)->change();
        });
    }
}
