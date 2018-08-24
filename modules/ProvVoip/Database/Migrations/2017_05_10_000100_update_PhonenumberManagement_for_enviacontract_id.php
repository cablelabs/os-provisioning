<?php

use Illuminate\Database\Schema\Blueprint;

/**
 * Updater to add col for related envia contract id
 *
 * @author Patrick Reichel
 */
class UpdatePhonenumberManagementForEnviacontractId extends BaseMigration
{
    // name of the table to create
    protected $tablename = 'phonenumbermanagement';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->tablename, function (Blueprint $table) {
            $table->integer('enviacontract_id')->unsigned()->nullable()->default(null);
        });

        $this->set_fim_fields([
            'subscriber_company',
            'subscriber_department',
            'subscriber_firstname',
            'subscriber_lastname',
            'subscriber_street',
            'subscriber_house_number',
            'subscriber_zip',
            'subscriber_city',
            'subscriber_district',
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
            $table->dropColumn('enviacontract_id');
        });

        $this->set_fim_fields([
            'subscriber_company',
            'subscriber_firstname',
            'subscriber_lastname',
            'subscriber_street',
            'subscriber_house_number',
            'subscriber_zip',
            'subscriber_city',
            'subscriber_district',
        ]);
    }
}
