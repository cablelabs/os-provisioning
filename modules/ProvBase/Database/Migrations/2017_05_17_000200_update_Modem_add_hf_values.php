<?php

use Illuminate\Database\Schema\Blueprint;

class UpdateModemAddHfValues extends BaseMigration
{
    // name of the table to create
    protected $tablename = 'modem';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->tablename, function (Blueprint $table) {
            $table->integer('us_snr')->after('status');
            $table->integer('ds_pwr')->after('us_snr');
            $table->integer('ds_snr')->after('ds_pwr');
            $table->renameColumn('status', 'us_pwr');
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
        Schema::table($this->tablename, function (Blueprint $table) {
            $table->renameColumn('us_pwr', 'status');
            $table->dropColumn(['us_snr', 'ds_pwr', 'ds_snr']);
        });
    }
}
