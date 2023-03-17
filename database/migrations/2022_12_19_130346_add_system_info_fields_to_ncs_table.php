<?php

use Database\Migrations\BaseMigration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSystemInfoFieldsToNcsTable extends BaseMigration
{
    public $migrationScope = 'database';

    protected $tableName = 'ncs';

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
            $table->dropColumn(['sys_uptime', 'serial_number', 'model', 'sw_ver']);
        });
    }
}
