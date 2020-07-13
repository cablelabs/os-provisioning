<?php

use Illuminate\Database\Schema\Blueprint;

class AddNetGwCoAPort extends BaseMigration
{
    protected $tablename = 'netgw';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->tablename, function (Blueprint $table) {
            $table->unsignedSmallInteger('coa_port')->nullable();
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
            $table->dropColumn('coa_port');
        });
    }
}
