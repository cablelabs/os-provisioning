<?php

use Illuminate\Database\Schema\Blueprint;

/**
 * Stepsize can be a decimal value
 */
class UpdateOIDChangeStepsize extends BaseMigration
{
    protected $tablename = 'oid';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->tablename, function (Blueprint $table) {
            $table->decimal('stepsize', 9, 4)->nullable()->change();
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
            $table->integer('stepsize')->nullable()->change();
        });
    }
}
