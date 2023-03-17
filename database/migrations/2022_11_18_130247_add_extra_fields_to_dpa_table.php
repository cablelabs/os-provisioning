<?php

use Database\Migrations\BaseMigration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExtraFieldsToDpaTable extends BaseMigration
{
    public $migrationScope = 'database';

    protected $tableName = 'dpa';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->tableName, function (Blueprint $table) {
            $table->integer('redundancy')->nullable();
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
            $table->dropColumn(['redundancy', 'cpu_utilization', 'memory_utilization']);
        });
    }
}
