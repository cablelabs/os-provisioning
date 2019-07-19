<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAlertsFieldsToProvbaseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('provbase', function (Blueprint $table) {
            $table->string('alert1')->default('');
            $table->string('alert2')->default('');
            $table->string('alert3')->default('');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('provbase', function (Blueprint $table) {
            $table->dropColumn(['alert1', 'alert2', 'alert3']);
        });
    }
}
