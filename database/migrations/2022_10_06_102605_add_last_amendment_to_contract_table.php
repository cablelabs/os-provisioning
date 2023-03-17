<?php

use Database\Migrations\BaseMigration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLastAmendmentToContractTable extends BaseMigration
{
    public $migrationScope = 'database';
    protected $tablename = 'contract';

    /**
     * Run the migrations.
     *
     * Last amendment is meant as date of last contract change regarding items which leads to an extended maturity/runtime
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->tablename, function (Blueprint $table) {
            $table->date('last_amendment')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table($this->tablename, function (Blueprint $table) {
            $table->dropColumn('last_amendment');
        });
    }
}
