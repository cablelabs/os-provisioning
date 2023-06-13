<?php

use Database\Migrations\BaseMigration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UdpdateSmartOntTableAddDefaultQosConfigfile extends BaseMigration
{
    public $migrationScope = 'database';

    protected $tableName = 'smartont';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->tableName, function (Blueprint $table) {
            $table->integer('default_configfile_id')->nullable();
            $table->integer('default_qos_id')->nullable();
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
        Schema::table($this->tableName, function (Blueprint $table) {
            $table->dropColumn([
                'default_configfile_id',
                'default_qos_id',
            ]);
        });
    }
}
