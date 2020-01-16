<?php

class RelationshipFixesHfcSnmp extends RelationshipFixes
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->upFixRelationshipTables('indices', ['netelement_id', 'parameter_id']);
        $this->upFixRelationshipTables('parameter', ['oid_id', 'netelementtype_id']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->downFixRelationshipTables('indices', ['netelement_id', 'parameter_id']);
        $this->downFixRelationshipTables('parameter', ['oid_id', 'netelementtype_id']);
    }
}
