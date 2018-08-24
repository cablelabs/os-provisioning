<?php

use Illuminate\Database\Schema\Blueprint;

class CreateMtaTable extends BaseMigration
{
    // name of the table to create
    protected $tablename = 'mta';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // creates directory for mta config files and changes owner
        $dir = '/tftpboot/mta';
        if (! is_dir($dir)) {
            mkdir($dir, '0755');
        }
        system('/bin/chown -R apache /tftpboot/mta');

        Schema::create($this->tablename, function (Blueprint $table) {
            $this->up_table_generic($table);

            $table->integer('modem_id')->unsigned()->default(1);
            $table->string('mac', 17);
            $table->string('hostname');
            $table->integer('configfile_id')->unsigned()->default(1);
            $table->enum('type', ['sip', 'packetcable']);
            $table->boolean('is_dummy')->default(0);
        });

        foreach ([1 => 'sip', 2 => 'packetcable'] as $i => $v) {
            DB::update('INSERT INTO '.$this->tablename." (hostname, type, is_dummy, deleted_at) VALUES('dummy-mta-".$v."',".$i.',1,NOW());');
        }

        $this->set_fim_fields(['mac', 'hostname']);
        $this->set_auto_increment(300000);

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
