<?php

use Database\Migrations\BaseMigration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SetNullableTemperatureIdAndUnitSensorTable extends BaseMigration
{
    public $migrationScope = 'database';

    protected $tableName = 'sensor';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->tableName, function (Blueprint $table) {
            $table->integer('temperature_id')->nullable()->change();
            $table->string('unit')->nullable()->change();
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
            $table->integer('temperature_id')->nullable(false)->change();
            $table->string('unit')->nullable(false)->change();
        });
    }
}
