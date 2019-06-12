<?php

use Illuminate\Database\Schema\Blueprint;

class CreateDocumentTable extends BaseMigration
{
    protected $tablename = 'document';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tablename, function (Blueprint $table) {
            $this->up_table_generic($table);

            $table->string('model')->nullable()->default(null);
            $table->integer('model_id')->unsigned()->nullable()->default(null);
            $table->integer('contract_id')->unsigned();
            $table->integer('documenttype_id')->unsigned();
            $table->string('file');
            $table->boolean('ccc_visible');
        });
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
