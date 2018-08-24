<?php

use Illuminate\Database\Schema\Blueprint;

/**
 * Updater to add envia related data to contract
 *
 * @author Patrick Reichel
 */
class UpdatePhonenumberManagementForSubscriberDistrict extends BaseMigration
{
    // name of the table to create
    protected $tablename = 'phonenumbermanagement';

    /**
     * Run the migrations.
     * For using the envia TEL API we need some changes in storing the contracts data.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->tablename, function (Blueprint $table) {
            $table->string('subscriber_district')->after('subscriber_city')->nullable()->default(null);
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
            $table->dropColumn('subscriber_district');
        });

        $this->set_fim_fields([
            'subscriber_company',
            'subscriber_firstname',
            'subscriber_lastname',
            'subscriber_street',
            'subscriber_house_number',
            'subscriber_zip',
            'subscriber_city',
        ]);
    }
}
