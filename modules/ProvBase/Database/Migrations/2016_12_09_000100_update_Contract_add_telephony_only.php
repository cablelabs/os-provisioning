<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateContractAddTelephonyOnly extends Migration
{
    /**
     * Run the migrations.
     * Customer has only subscribed telephony, i.e. no internet access
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contract', function (Blueprint $table) {
            $table->boolean('telephony_only');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contract', function (Blueprint $table) {
            $table->dropColumn(['telephony_only']);
        });
    }
}
