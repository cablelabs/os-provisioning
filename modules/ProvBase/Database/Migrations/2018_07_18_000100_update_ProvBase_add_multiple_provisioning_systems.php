<?php

use Illuminate\Database\Schema\Blueprint;

class UpdateProvBaseAddMultipleProvisioningSystems extends BaseMigration
{
    // name of the table to create
    protected $tablename = 'provbase';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->tablename, function (Blueprint $table) {
            $table->boolean('multiple_provisioning_systems');
        });

        DB::update("UPDATE $this->tablename SET multiple_provisioning_systems = 0;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table($this->tablename, function (Blueprint $table) {
            $table->dropColumn('multiple_provisioning_systems');
        });
    }
}
