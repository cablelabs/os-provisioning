<?php

use Illuminate\Database\Schema\Blueprint;

class CreateDeviceTypeTable extends BaseMigration
{
    // name of the table to create
    protected $tablename = 'devicetype';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(strtolower('DeviceType'), function (Blueprint $table) {
            $this->up_table_generic($table);

            $table->string('name');
            $table->string('vendor');
            $table->string('version');
            $table->text('description');
            $table->integer('parent_id')->unsigned();
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
        Schema::drop(strtolower('DeviceType'));
    }
}
