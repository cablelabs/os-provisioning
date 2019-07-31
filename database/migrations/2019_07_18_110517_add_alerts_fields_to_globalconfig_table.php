<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAlertsFieldsToGlobalconfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('global_config', function (Blueprint $table) {
            $table->string('alert1')->nullable();
            $table->string('alert2')->nullable();
            $table->string('alert3')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('global_config', function (Blueprint $table) {
            $table->dropColumn(['alert1', 'alert2', 'alert3']);
        });
    }
}
