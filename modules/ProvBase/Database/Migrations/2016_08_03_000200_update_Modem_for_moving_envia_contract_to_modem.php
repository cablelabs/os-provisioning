<?php

use Illuminate\Database\Schema\Blueprint;

/**
 * envia TEL contracts match our Modems (=telephone connection) we move external contract stuff to modem…
 *
 * @author Patrick Reichel
 */
class UpdateModemForMovingEnviaContractToModem extends BaseMigration
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
            $table->string('contract_external_id')->nullable()->default(null)->after('contract_id');
            $table->date('contract_ext_creation_date')->nullable()->default(null)->after('contract_external_id');
            $table->date('contract_ext_termination_date')->nullable()->default(null)->after('contract_ext_creation_date');
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
                'contract_external_id',
                'contract_ext_creation_date',
                'contract_ext_termination_date',
            ]);
        });

        // give all cols to be indexed (old and new ones => the index will be dropped and then created from scratch)
        $this->set_fim_fields([
            'name',
            'hostname',
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
