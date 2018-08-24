<?php

use Illuminate\Database\Schema\Blueprint;

/**
 * envia TEL contracts match our Modems (=telephone connection) we move external contract stuff to modem…
 *
 * @author Patrick Reichel
 */
class UpdateModemAddInstallationAddressChangeDate extends BaseMigration
{
    // name of the table to create
    protected $tablename = 'modem';

    /**
     * Run the migrations.
     * For using the envia TEL API we need some changes in storing the contracts data.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->tablename, function (Blueprint $table) {
            $table->date('installation_address_change_date')->nullable()->default(null)->after('country_id');
        });

        // give all cols to be indexed (old and new ones => the index will be dropped and then created from scratch)
        $this->set_fim_fields([
            'name',
            'hostname',
            'contract_external_id',
            'firstname',
            'lastname',
            'street',
            'house_number',
            'zip',
            'city',
            'district',
            'birthday',
            'company',
            'department',
            'mac',
            'serial_num',
            'inventar_num',
            'description',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table($this->tablename, function (Blueprint $table) {
            $table->dropColumn([
                'installation_address_change_date',
            ]);
        });

        // give all cols to be indexed (old and new ones => the index will be dropped and then created from scratch)
        $this->set_fim_fields([
            'name',
            'hostname',
            'contract_external_id',
            'firstname',
            'lastname',
            'street',
            'house_number',
            'zip',
            'city',
            'district',
            'birthday',
            'company',
            'department',
            'mac',
            'serial_num',
            'inventar_num',
            'description',
        ]);
    }
}
