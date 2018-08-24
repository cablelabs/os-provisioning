<?php

use Illuminate\Database\Schema\Blueprint;

class UpdateSnmpValueTable extends BaseMigration
{
    // name of the table to update
    protected $tablename = 'snmpvalue';

    /**
     * Run the migrations - Rename tree to netelement and merge both tables
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->tablename, function (Blueprint $table) {
            $table->renameColumn('snmpmib_id', 'oid_id');
            $table->renameColumn('device_id', 'netelement_id');
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
            $table->renameColumn('oid_id', 'snmpmib_id');
            $table->renameColumn('netelement_id', 'device_id');
        });
    }
}
