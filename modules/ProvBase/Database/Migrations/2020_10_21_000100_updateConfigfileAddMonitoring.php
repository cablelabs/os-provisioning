<?php

use Illuminate\Database\Schema\Blueprint;

class UpdateConfigfileAddMonitoring extends BaseMigration
{
    /**
     * Run the migrations
     *
     * @return void
     */
    public function up()
    {
        Schema::table('configfile', function (Blueprint $table) {
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
        Schema::table('configfile', function (Blueprint $table) {
            $table->dropColumn('monitoring');
        });
    }
}
