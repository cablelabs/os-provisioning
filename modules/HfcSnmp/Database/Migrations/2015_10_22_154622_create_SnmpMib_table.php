<?php

use Illuminate\Database\Schema\Blueprint;

class CreateSnmpMibTable extends BaseMigration
{
    // name of the table to create
    protected $tablename = 'snmpmib';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('snmpmib', function (Blueprint $table) {
            $this->up_table_generic($table);

            $table->integer('devicetype_id')->unsigned();
            $table->enum('html_type', ['text', 'select', 'groupbox', 'textarea']);
            $table->string('html_frame', 16);
            $table->text('html_properties');
            $table->integer('html_id')->unsigned(); // for feature use
            $table->string('field');
            $table->string('oid');
            $table->boolean('oid_table');
            $table->enum('type', ['i', 'u', 's', 'x', 'd', 'n', 'o', 't', 'a', 'b']);
            $table->string('type_array');
            $table->text('phpcode_pre');
            $table->text('phpcode_post');
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
        Schema::drop('snmpmib');
    }
}
