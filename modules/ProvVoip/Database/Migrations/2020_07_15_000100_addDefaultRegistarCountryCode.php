<?php

use Illuminate\Database\Schema\Blueprint;

class AddDefaultRegistarCountryCode extends \BaseMigration
{
    // name of the table to create
    protected $tablename = 'provvoip';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->tablename, function (Blueprint $table) {
            $table->string('default_sip_registrar')->nullable();
            $table->string('default_country_code')->nullable();
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
            $table->dropColumn(['default_sip_registrar', 'default_country_code']);
        });
    }
}
