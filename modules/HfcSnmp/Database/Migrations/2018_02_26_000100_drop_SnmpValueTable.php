<?php

use Illuminate\Database\Schema\Blueprint;

class DropSnmpValueTable extends BaseMigration
{
    // name of the table to create
    protected $tablename = 'snmpvalue';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::drop(strtolower('SnmpValue'));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create(strtolower('SnmpValue'), function (Blueprint $table) {
            $this->up_table_generic($table);

            $table->integer('device_id')->unsigned();
            $table->integer('snmpmib_id')->unsigned();
            $table->string('oid_index'); 			// for table elements
            $table->string('value');
        });

        return parent::up();
    }
}
