<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Updater to add link to chosen purchase tariff (= variation @Envia)
 *
 * @author Patrick Reichel
 */
class UpdateContractForTariffInformationTable extends BaseMigration {

	// name of the table to create
	protected $tablename = "contract";


    /**
	 * Run the migrations.
	 * For using the Envia API we need some changes in storing the contracts data.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->tablename, function(Blueprint $table)
        {

			// not needed anymore – will be part of phonenumbermanagement
			$table->dropColumn('phonebook_entry');

			// this will holds the reference to purchase tariff (the tariff between external provider and us)
			$table->integer('purchase_tariff')->after('network_access')->nullable()->default(NULL);

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
				'purchase_tariff',
			]);

			$table->boolean('phonebook_entry')->after('network_access');
        });
    }

}
