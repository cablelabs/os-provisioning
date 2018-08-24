<?php

use Illuminate\Database\Schema\Blueprint;

class CreateConfigfileTable extends BaseMigration
{
    // name of the table to create
    protected $tablename = 'configfile';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // creates directory for firmware files and changes owner
        $dir = '/tftpboot/fw';
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        system('/bin/chown -R apache '.$dir);

        Schema::create($this->tablename, function (Blueprint $table) {
            $this->up_table_generic($table);

            $table->string('name');
            $table->text('text');
            $table->enum('type', ['generic', 'network', 'vendor', 'user']);
            $table->enum('device', ['cm', 'mta']);
            $table->enum('public', ['yes', 'no']);
            $table->integer('parent_id')->unsigned();
            $table->string('firmware')->default('');
            $table->boolean('is_dummy')->default(0);
        });

        $this->set_fim_fields(['name', 'text', 'firmware']);

        // TODO: should this be moved to seeding ?
        foreach ([1 => 'cm', 2 => 'mta'] as $i => $v) {
            DB::update('INSERT INTO '.$this->tablename." (name, device, is_dummy, deleted_at) VALUES('dummy-cfg-".$v."',".$i.',1,NOW());');
        }

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

        // remove all config and firmware files
        $files = [];
        $files['cm'] = glob('/tftpboot/cm/*');              // get all files in dir
        $files['mta'] = glob('/tftpboot/mta/*');              // get all files in dir
        $files['fw'] = glob('/tftpboot/fw/*');              // get all files in dir

        foreach ($files as $type) {
            foreach ($type as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
        }
    }
}
