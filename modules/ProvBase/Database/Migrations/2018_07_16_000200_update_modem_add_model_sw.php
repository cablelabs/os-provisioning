<?php

use Illuminate\Database\Schema\Blueprint;

/*
 * MPR: Modem Positioning Rule
 */
class UpdateModemAddModelSw extends BaseMigration
{
    protected $tablename = 'modem';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->tablename, function (Blueprint $table) {
            $table->string('model')->nullable()->default(null);
            $table->string('sw_rev')->nullable()->default(null);
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
            $table->dropColumn(['model', 'sw_rev']);
        });
    }
}
