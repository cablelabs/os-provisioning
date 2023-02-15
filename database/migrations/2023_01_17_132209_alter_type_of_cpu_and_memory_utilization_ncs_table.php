<?php

use Database\Migrations\BaseMigration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class AlterTypeOfCpuAndMemoryUtilizationNcsTable extends BaseMigration
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
            $table->float('cpu_utilization', 8, 2)->change();
            $table->float('memory_utilization', 8, 2)->change();
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
            $table->integer('cpu_utilization')->change();
            $table->integer('memory_utilization')->change();
        });
    }
}
