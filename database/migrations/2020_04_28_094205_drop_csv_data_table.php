<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class DropCsvDataTable extends BaseMigration
{
    protected $tablename = 'csv_data';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists($this->tablename);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create($this->tablename, function (Blueprint $table) {
            $this->up_table_generic($table);

            $table->string('csv_filename');
            $table->boolean('csv_header')->default(0);
            $table->boolean('observer')->default(0);
            $table->longText('csv_data');
        });
    }
}
