<?php

use Database\Migrations\BaseMigration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSummaryMetricsFieldsToNcsTable extends BaseMigration
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
            $table->integer('cpu_utilization')->nullable();
            $table->integer('memory_utilization')->nullable();
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
