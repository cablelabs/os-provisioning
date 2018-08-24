<?php

use Illuminate\Database\Schema\Blueprint;

class CreateProvVoipTable extends \BaseMigration
{
    // name of the table to create
    protected $tablename = 'provvoip';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tablename, function (Blueprint $table) {
            $this->up_table_generic($table);

            $table->integer('startid_mta')->unsigned();
        });

        DB::update('INSERT INTO '.$this->tablename.' (startid_mta) VALUES(300000);');

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
