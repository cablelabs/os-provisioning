<?php

use Illuminate\Database\Schema\Blueprint;

class CreateGuiLogTable extends BaseMigration
{
    // name of the table to create
    protected $tablename = 'guilog';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tablename, function (Blueprint $table) {
            $this->up_table_generic($table);

            $table->integer('authuser_id');
            $table->string('username');
            $table->string('method');
            $table->string('model');
            $table->string('model_id');
            $table->text('text');
        });

        $this->set_fim_fields(['username', 'method', 'model', 'text']);

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
