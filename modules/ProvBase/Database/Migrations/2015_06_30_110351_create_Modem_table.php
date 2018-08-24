<?php

use Illuminate\Database\Schema\Blueprint;

class CreateModemTable extends BaseMigration
{
    // name of the table to create
    protected $tablename = 'modem';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // creates directory for modem config files and changes owner
        $dir = '/tftpboot/cm';
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        system('/bin/chown -R apache /etc/dhcp-nmsprime/ '.$dir);

        Schema::create($this->tablename, function (Blueprint $table) {
            $this->up_table_generic($table);

            $table->string('name');
            $table->string('hostname');
            $table->integer('contract_id')->unsigned();
            $table->string('firstname');
            $table->string('lastname');
            $table->string('street');
            $table->string('zip', 16);
            $table->string('city');
            $table->integer('country_id')->unsigned();
            $table->string('mac')->sizeof(17);
            $table->integer('status');
            $table->boolean('public');
            $table->boolean('network_access');
            $table->string('serial_num');
            $table->string('inventar_num');
            $table->text('description');
            $table->integer('parent');
            $table->integer('configfile_id')->unsigned();
            $table->integer('tree_id')->unsigned();
            $table->integer('qos_id')->unsigned();
            $table->float('x');
            $table->float('y');
            $table->string('number', 32);	// placeholder: used for km3 import
        });

        $this->set_fim_fields(['name', 'hostname', 'mac', 'serial_num', 'inventar_num', 'description']);
        $this->set_auto_increment(100000);

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
