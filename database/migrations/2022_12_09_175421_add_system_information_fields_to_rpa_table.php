<?php

use Database\Migrations\BaseMigration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class AddSystemInformationFieldsToRpaTable extends BaseMigration
{
    public $migrationScope = 'database';

    protected $tableName = 'rpa';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->tableName, function (Blueprint $table) {
            $table->string('sys_uptime')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('model')->nullable();
            $table->string('last_conf_change')->nullable();
            $table->text('sw_ver')->nullable();
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
            $table->dropColumn(['sys_uptime', 'serial_number', 'model', 'last_conf_change', 'sw_ver']);
        });
    }
}
