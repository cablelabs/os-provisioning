<?php

use Illuminate\Database\Schema\Blueprint;

class CsvData extends BaseMigration
{
    // name of the table to create
    protected $tablename = 'csv_data';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tablename, function (Blueprint $table) {
            $this->up_table_generic($table);

            $table->string('csv_filename');
            $table->boolean('csv_header')->default(0);
            $table->boolean('observer')->default(0);
            $table->longText('csv_data');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('csv_data');
    }
}
