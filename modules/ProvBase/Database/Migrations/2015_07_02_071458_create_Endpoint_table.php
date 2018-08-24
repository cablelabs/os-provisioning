<?php

use Illuminate\Database\Schema\Blueprint;

class CreateEndpointTable extends BaseMigration
{
    // name of the table to create
    protected $tablename = 'endpoint';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tablename, function (Blueprint $table) {
            $this->up_table_generic($table);

            $table->string('hostname');
            $table->string('name');
            $table->string('mac', 17);
            $table->text('description');
            $table->enum('type', ['cpe', 'mta']);
            $table->boolean('public');

            // $table->integer('modem_id')->unsigned(); // depracted
        });

        $this->set_fim_fields(['hostname', 'name', 'description']);
        $this->set_auto_increment(200000);

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
