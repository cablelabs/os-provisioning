<?php

use Database\Migrations\BaseMigration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeviceTypeModelCitySiteToCcap extends BaseMigration
{
    public $migrationScope = 'database';

    protected $tableName = 'ccap';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->tableName, function (Blueprint $table) {
            $table->string('device_type')->nullable();
            $table->string('model')->nullable();
            $table->string('city')->nullable();
            $table->string('site')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table($this->tableName, function (Blueprint $table) {
            $table->dropColumn('device_type');
            $table->dropColumn('model');
            $table->dropColumn('city');
            $table->dropColumn('site');
        });
    }
}
