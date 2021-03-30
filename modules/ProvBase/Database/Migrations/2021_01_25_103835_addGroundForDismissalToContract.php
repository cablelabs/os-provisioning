<?php

use Illuminate\Database\Schema\Blueprint;

class AddGroundForDismissalToContract extends BaseMigration
{
    protected $tableName = 'contract';
    protected $columnName = 'ground_for_dismissal';
    public $migrationScope = 'database';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->tableName, function (Blueprint $table) {
            $table->string($this->columnName)->nullable();
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
            $table->dropColumn($this->columnName);
        });
    }
}
