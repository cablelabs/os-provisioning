<?php

use Illuminate\Database\Schema\Blueprint;

class UpdateContractPhonenumberNullable extends BaseMigration
{
    public $migrationScope = 'database';

    protected $tablename = 'contract';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->tablename, function (Blueprint $table) {
            $table->string('phone', 100)->nullable()->change();
        });

        return parent::up();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table($this->tablename, function (Blueprint $table) {
            $table->string('phone', 100)->nullable(false)->change();
        });
    }
}
