<?php

use Illuminate\Database\Schema\Blueprint;

class addRandomIpAllocation extends BaseMigration
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
            $table->boolean('random_ip_allocation')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table($this->tablename, function (Blueprint $table) {
            $table->dropColumn('random_ip_allocation');
        });
    }
}
