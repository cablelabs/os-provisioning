<?php

use Database\Migrations\BaseMigration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExtraFieldsToSensorTable extends BaseMigration
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
            $table->integer('cpu_util')->nullable();
            $table->integer('buffer_util')->nullable();
            $table->string('fru_status')->nullable();
            $table->string('serial_num')->nullable();
            $table->string('revision')->nullable();
            $table->string('part_num')->nullable();
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
            $table->dropColumn(['cpu_util', 'buffer_util', 'fru_status', 'serial_num', 'revision', 'part_num']);
        });
    }
}
