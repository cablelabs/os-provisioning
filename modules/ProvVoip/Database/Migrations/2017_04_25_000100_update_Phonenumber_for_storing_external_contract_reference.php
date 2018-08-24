<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Updater to add col for external contract identifier to phonenumber
 *
 * @author Patrick Reichel
 */
class UpdatePhonenumberForStoringExternalContractReference extends Migration
{
    // name of the table to update
    protected $tablename = 'phonenumber';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->tablename, function (Blueprint $table) {
            $table->string('contract_external_id')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table($this->tablename, function (Blueprint $table) {
            $table->dropColumn('contract_external_id');
        });
    }
}
