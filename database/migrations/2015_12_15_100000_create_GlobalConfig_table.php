<?php

use Illuminate\Database\Schema\Blueprint;

class CreateGlobalConfigTable extends BaseMigration
{
    // name of the table to create
    protected $tablename = 'global_config';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tablename, function (Blueprint $table) {
            $this->up_table_generic($table);

            $table->string('name');
            $table->string('street');
            $table->string('city');
            $table->string('phone');
            $table->string('mail');
            $table->integer('log_level');
            $table->string('headline1');
            $table->string('headline2');
        });

        DB::update('INSERT INTO '.$this->tablename." (log_level, headline1, headline2) VALUES(1, 'NMS Prime', 'The next Generation NMS');");

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
