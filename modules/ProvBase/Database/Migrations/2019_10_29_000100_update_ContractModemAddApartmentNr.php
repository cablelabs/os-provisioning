<?php

use Illuminate\Database\Schema\Blueprint;

class UpdateContractModemAddApartment extends BaseMigration
{
    /**
     * Run the migrations. Apartment nr is needed if PropertyManagement module is disabled
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contract', function (Blueprint $table) {
            $table->string('apartment_nr')->nullable();
        });

        Schema::table('modem', function (Blueprint $table) {
            $table->string('apartment_nr')->nullable();
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
            $table->dropColumn('apartment_nr');
        });

        Schema::table('modem', function (Blueprint $table) {
            $table->dropColumn('apartment_nr');
        });
    }
}
