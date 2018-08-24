<?php

use Illuminate\Database\Schema\Blueprint;

class CreateQosTable extends BaseMigration
{
    // name of the table to create
    protected $tablename = 'qos';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tablename, function (Blueprint $table) {
            $this->up_table_generic($table);

            $table->float('ds_rate_max');
            $table->float('us_rate_max');
            $table->integer('ds_rate_max_help')->unsigned();
            $table->integer('us_rate_max_help')->unsigned();
            $table->string('name');
        });

        $this->set_fim_fields(['name']);

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
