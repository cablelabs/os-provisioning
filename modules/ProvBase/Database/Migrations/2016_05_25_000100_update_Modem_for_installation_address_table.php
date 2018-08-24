<?php

use Illuminate\Database\Schema\Blueprint;

/**
 * Updater to add envia related data to contract
 *
 * @author Patrick Reichel
 */
class UpdateModemForInstallationAddressTable extends BaseMigration
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
            $table->string('salutation')->after('contract_id');
            $table->string('company')->after('salutation');
            $table->string('department')->after('company');
            $table->string('house_number', 8)->after('street');
            $table->string('district')->after('city');
            $table->string('birthday')->after('district');
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

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table($this->tablename, function (Blueprint $table) {
            $table->dropColumn([
                'salutation',
                'house_number',
                'district',
                'birthday',
                'company',
                'department',
            ]);
        });
    }
}
