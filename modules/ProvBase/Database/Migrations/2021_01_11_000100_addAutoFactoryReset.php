<?php

use Illuminate\Database\Schema\Blueprint;

class addAutoFactoryReset extends BaseMigration
{
    protected $tablename = 'provbase';
    public $migrationScope = 'database';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->tablename, function (Blueprint $table) {
            $table->boolean('auto_factory_reset')->default(0);
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
            $table->dropColumn('auto_factory_reset');
        });
    }
}
