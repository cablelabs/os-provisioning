<?php

use Illuminate\Database\Schema\Blueprint;

class UpdateEndpointMacNullable extends BaseMigration
{
    protected $tablename = 'endpoint';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->tablename, function (Blueprint $table) {
            $table->string('mac')->sizeof(17)->nullable()->change();
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
            $table->string('mac')->sizeof(17)->change();
        });
    }
}
