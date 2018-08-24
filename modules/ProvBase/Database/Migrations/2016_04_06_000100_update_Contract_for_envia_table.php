<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Updater to add envia related data to contract
 *
 * @author Patrick Reichel
 */
class UpdateContractForEnviaTable extends BaseMigration
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
        Schema::table('contract', function (Blueprint $table) {
            $table->string('number', 32)->change();	// was integer in original migration
            $table->string('number3', 32)->nullable()->default(null)->after('number2');
            $table->string('number4', 32)->nullable()->default(null)->after('number3');
            $table->string('contract_external_id', 60)->nullable()->default(null)->after('number4');
            $table->string('customer_external_id', 60)->nullable()->default(null)->after('contract_external_id');
            $table->date('contract_ext_creation_date')->nullable()->default(null)->after('customer_external_id');
            $table->date('contract_ext_termination_date')->nullable()->default(null)->after('contract_ext_creation_date');
            $table->string('academic_degree')->after('salutation');
            $table->string('house_number', 8)->after('street');
            $table->boolean('phonebook_entry')->after('network_access');
            $table->date('contract_end')->nullable()->default(null)->change();
            $table->string('password', 64)->change();
        });

        // give all cols to be indexed (old and new ones => the index will be dropped and then created from scratch)
        $this->set_fim_fields([
            'number2',
            'company',
            'firstname',
            'lastname',
            'street',
            'zip',
            'city',
            'phone',
            'fax',
            'email',
            'description',
            'sepa_iban',

            'number3',
            'number4',
            'contract_external_id',
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
        Schema::table('contract', function (Blueprint $table) {
            $table->dropColumn([
                'number3',
                'number4',
                'contract_external_id',
                'customer_external_id',
                'contract_ext_creation_date',
                'contract_ext_termination_date',
                'academic_degree',
                'house_number',
                'phonebook_entry',
            ]);

            $table->integer('number')->change();
            $table->string('password', 32)->change();
        });
    }
}
