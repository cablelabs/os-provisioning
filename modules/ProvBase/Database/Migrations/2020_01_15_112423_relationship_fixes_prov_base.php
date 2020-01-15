<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class RelationshipFixesProvBase extends RelationshipFixes
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->upFixRelationshipTables('contract', ['country_id', 'qos_id', 'next_qos_id']);
        $this->upFixRelationshipTables('ippool', ['netgw_id']);
        $this->upFixRelationshipTables('modem', ['country_id', 'qos_id', 'netelement_id']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->downFixRelationshipTables('contract', ['country_id', 'qos_id', 'next_qos_id']);
        $this->downFixRelationshipTables('ippool', ['netgw_id']);
        $this->downFixRelationshipTables('modem', ['country_id', 'qos_id', 'netelement_id']);
    }
}
