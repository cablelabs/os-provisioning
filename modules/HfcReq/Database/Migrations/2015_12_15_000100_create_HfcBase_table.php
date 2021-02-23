<?php

use Illuminate\Database\Schema\Blueprint;

class CreateHfcBaseTable extends BaseMigration
{
    // name of the table to create
    protected $tablename = 'hfcbase';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tablename, function (Blueprint $table) {
            $this->up_table_generic($table);

            $table->string('ro_community');
            $table->string('rw_community');
        });

        DB::update('INSERT INTO '.$this->tablename." (ro_community, rw_community) VALUES('public', 'private');");

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
