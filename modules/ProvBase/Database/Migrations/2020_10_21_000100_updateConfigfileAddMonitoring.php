<?php

use Illuminate\Database\Schema\Blueprint;

class UpdateConfigfileAddMonitoring extends BaseMigration
{
    protected $tablename = 'configfile';

    /**
     * Run the migrations
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->tablename, function (Blueprint $table) {
            $table->text('monitoring')->nullable();
        });
    }

    /**
     * Reverse the migrations
     *
     * @return void
     */
    public function down()
    {
        Schema::table($this->tablename, function (Blueprint $table) {
            $table->dropColumn('monitoring');
        });
    }
}
