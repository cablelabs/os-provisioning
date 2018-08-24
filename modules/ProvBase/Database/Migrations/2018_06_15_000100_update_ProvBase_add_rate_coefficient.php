<?php

use Illuminate\Database\Schema\Blueprint;

class UpdateProvBaseAddRateCoefficient extends BaseMigration
{
    // name of the table to create
    protected $tablename = 'provbase';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->tablename, function (Blueprint $table) {
            $table->float('ds_rate_coefficient');
            $table->float('us_rate_coefficient');
        });

        DB::update("UPDATE $this->tablename SET ds_rate_coefficient = 1;");
        DB::update("UPDATE $this->tablename SET us_rate_coefficient = 1;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table($this->tablename, function (Blueprint $table) {
            $table->dropColumn(['ds_rate_coefficient', 'us_rate_coefficient']);
        });
    }
}
