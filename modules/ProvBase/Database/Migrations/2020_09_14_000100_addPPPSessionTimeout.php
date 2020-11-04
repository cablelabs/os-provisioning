<?php

use Illuminate\Database\Schema\Blueprint;

class addPPPSessionTimeout extends BaseMigration
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
            $table->integer('ppp_session_timeout')->unsigned()->default(86400);
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
            $table->dropColumn('ppp_session_timeout');
        });
    }
}
