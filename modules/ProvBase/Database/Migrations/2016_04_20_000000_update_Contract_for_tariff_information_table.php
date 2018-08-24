<?php

use Illuminate\Database\Schema\Blueprint;

/**
 * Updater to add link to chosen purchase tariff (= variation at envia TEL)
 *
 * @author Patrick Reichel
 */
class UpdateContractForTariffInformationTable extends BaseMigration
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

            // not needed anymore – will be part of phonenumbermanagement
            $table->dropColumn('phonebook_entry');

            // this will hold the reference to purchase tariff (the tariff between external provider and us)
            $table->integer('purchase_tariff')->after('network_access')->nullable()->default(null);

            // this will hold the reference to next purchase tariff (the tariff between external provider and us)
            $table->integer('next_purchase_tariff')->after('purchase_tariff')->nullable()->default(null);

            // new in envia API 1.4
            $table->string('district')->after('city');
            $table->string('department')->after('company');
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
        Schema::table($this->tablename, function (Blueprint $table) {
            $table->dropColumn([
                'purchase_tariff',
                'next_purchase_tariff',
                'district',
                'department',
            ]);

            $table->boolean('phonebook_entry')->after('network_access');
        });
    }
}
