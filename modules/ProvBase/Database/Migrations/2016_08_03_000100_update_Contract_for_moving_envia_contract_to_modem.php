<?php

use Illuminate\Database\Schema\Blueprint;

/**
 * envia TEL contracts match our Modems (=telephone connection) we move external contract stuff to modem…
 *
 * @author Patrick Reichel
 */
class UpdateContractForMovingEnviaContractToModem extends BaseMigration
{
    // name of the table to create
    protected $tablename = 'contract';

    /**
     * Run the migrations.
     * For using the envia TEL API we need some changes in storing the contracts data.
     *
     * @return void
     */
    public function up()
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
            'number2',
            'company',
            'department',
            'firstname',
            'lastname',
            'street',
            'zip',
            'city',
            'district',
            'phone',
            'fax',
            'email',
            'description',
            'sepa_iban',
            'number3',
            'number4',
            'customer_external_id',
            'academic_degree',
            'house_number',
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
            $table->string('contract_external_id')->nullable()->default(null)->after('number4');
            $table->date('contract_ext_creation_date')->nullable()->default(null)->after('contract_external_id');
            $table->date('contract_ext_termination_date')->nullable()->default(null)->after('contract_ext_creation_date');
        });

        // give all cols to be indexed (old and new ones => the index will be dropped and then created from scratch)
        $this->set_fim_fields([
            'number2',
            'company',
            'department',
            'firstname',
            'lastname',
            'street',
            'zip',
            'city',
            'district',
            'phone',
            'fax',
            'email',
            'description',
            'sepa_iban',
            'number3',
            'number4',
            'customer_external_id',
            'contract_external_id',
            'academic_degree',
            'house_number',
        ]);
    }
}
