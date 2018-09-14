<?php

use Illuminate\Database\Schema\Blueprint;

class CreateSlaTable extends BaseMigration
{
    // name of the table to create
    protected $tablename = 'sla';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tablename, function (Blueprint $table) {
            $this->up_table_generic($table);
            $table->string('name')->nullable();
            $table->string('license')->nullable();
            // $table->mediumInteger('num_cmts')->nullable();
            // $table->integer('num_contracts')->nullable();   // TV customers ??
            // $table->integer('num_modems')->nullable();
            // $table->integer('num_netelements')->nullable();
            // $table->string('system_status')->nullable();
        });

        DB::table($this->tablename)->insert(['name' => null]);
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
