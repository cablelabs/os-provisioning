<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class RelationshipFixesHfcReq extends RelationshipFixes
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->upFixRelationshipTables('netelementtype', ['pre_conf_oid_id']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->downFixRelationshipTables('netelementtype', ['pre_conf_oid_id']);
    }
}
