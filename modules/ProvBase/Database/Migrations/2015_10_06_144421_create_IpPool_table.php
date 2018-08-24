<?php

use Illuminate\Database\Schema\Blueprint;

class CreateIpPoolTable extends BaseMigration
{
    // name of the table to create
    protected $tablename = 'ippool';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tablename, function (Blueprint $table) {
            $this->up_table_generic($table);

            $table->integer('cmts_id')->unsigned();
            $table->enum('type', ['CM', 'CPEPub', 'CPEPriv', 'MTA']); 	// (cm, cpePub, cpePriv, mta)
            $table->string('net')->sizeof(20);
            $table->string('netmask')->sizeof(20);
            $table->string('ip_pool_start')->sizeof(20);
            $table->string('ip_pool_end')->sizeof(20);
            $table->string('router_ip')->sizeof(20);
            $table->string('broadcast_ip')->sizeof(20);
            $table->string('dns1_ip')->sizeof(20);
            $table->string('dns2_ip')->sizeof(20);
            $table->string('dns3_ip')->sizeof(20);
            $table->text('optional');
            $table->text('description');
        });

        $this->set_fim_fields(['net', 'netmask', 'ip_pool_start', 'ip_pool_end', 'router_ip', 'broadcast_ip', 'dns1_ip', 'dns2_ip', 'dns3_ip', 'optional']);

        return parent::up();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop($this->tablename);
    }
}
