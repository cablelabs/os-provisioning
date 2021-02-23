<?php

use Illuminate\Database\Schema\Blueprint;

class RenameTableHfcBaseToHfcReq extends BaseMigration
{
    protected $tablename = 'hfcbase';
    public $migrationScope = 'database';

    /**
     * Current fields like SNMP communities and infos of Satkabel tap controlling infrastructure
     * are needed only in HfcSnmp & Satkabel module which both depend on HfcReq with is Open Source
     * instead of HfcBase
     *
     * @return void
     */
    public function up()
    {
        Schema::rename($this->tablename, 'hfcreq');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename('hfcreq', $this->tablename);
    }
}
