<?php

use Database\Migrations\BaseMigration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSummaryMetricsFieldsToRpaTable extends BaseMigration
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
            $table->string('cpu_utilization')->nullable();
            $table->string('memory_utilization')->nullable();
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
            $table->dropColumn(['cpu_utilization', 'memory_utilization']);
        });
    }
}
