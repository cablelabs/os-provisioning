<?php

use Illuminate\Database\Schema\Blueprint;

class UpdateModemAddTdr extends BaseMigration
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
            $table->float('tdr');
            $table->float('fft_max');
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
            $table->dropColumn('tdr');
            $table->dropColumn('fft_max');
        });
    }
}
