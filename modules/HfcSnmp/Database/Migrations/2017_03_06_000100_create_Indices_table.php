<?php

use Illuminate\Database\Schema\Blueprint;

/**
 * This is used for Specifying Indices for a Parameter of a NetElement
 */
class CreateIndicesTable extends BaseMigration
{
    // name of the table to create
    protected $tablename = 'indices';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tablename, function (Blueprint $table) {
            $this->up_table_generic($table);

            $table->integer('netelement_id')->unsigned();
            $table->integer('parameter_id')->unsigned();
            $table->string('indices');
        });

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
