<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateProvBaseAddMaxCpe extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('provbase', function (Blueprint $table) {
            $table->smallInteger('max_cpe')->nullable();
        });

        // no where filter is set, as there should be only one row with id 1
        DB::update("UPDATE provbase SET max_cpe = '2';");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('provbase', function (Blueprint $table) {
            $table->dropColumn(['max_cpe']);
        });
    }
}
