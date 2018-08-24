<?php

use Illuminate\Database\Schema\Blueprint;

class CreateMibFileTable extends BaseMigration
{
    // name of the table to create
    protected $tablename = 'mibfile';

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
            $table->string('filename');
            $table->string('version');
            $table->text('description');
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
