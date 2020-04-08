<?php

use Illuminate\Database\Schema\Blueprint;

class AddDsUSName extends BaseMigration
{
    // name of the table to create
    protected $tablename = 'qos';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->tablename, function (Blueprint $table) {
            $table->string('ds_name')->nullable();
            $table->string('us_name')->nullable();
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
            $table->dropColumn(['ds_name', 'us_name']);
        });
    }
}
