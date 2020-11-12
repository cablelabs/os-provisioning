<?php

use Illuminate\Database\Schema\Blueprint;

class updateNetGwAddIpv6 extends BaseMigration
{
    // name of the table to create
    protected $tablename = 'netgw';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->tablename, function (Blueprint $table) {
            $table->string('ipv6')->nullable();
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
            $table->dropColumn('ipv6');
        });
    }
}
