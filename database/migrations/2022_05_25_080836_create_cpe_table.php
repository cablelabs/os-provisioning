<?php

use Database\Migrations\BaseMigration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCpeTable extends BaseMigration
{
    public $migrationScope = 'database';

    protected $tableName = 'cpe';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->increments('id');
            $table->engine = 'InnoDB';
            $table->timestampsTz(null);
            $table->softDeletesTz('deleted_at', null);
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->integer('netelement_id');

            $table->string('mac', 17)->nullable();
            $table->string('ip', 64)->nullable();
            $table->string('status', 64)->nullable();
            $table->timestamp('last_reg_time', null);
            $table->string('last_mac_state', 64)->nullable();
            $table->string('ds_data_service_grp', 64)->nullable();
            $table->string('us_data_service_grp', 64)->nullable();
            $table->string('ccap_core', 64)->nullable();
            $table->string('device_class', 64)->nullable();
            $table->string('core_ipv6', 64)->nullable();
            $table->string('manufacturer_name', 64)->nullable();
            $table->integer('fiber_node_sys_id')->nullable();
            $table->string('fiber_node_sys_name', 64)->nullable();
            $table->string('reg_ver', 64)->nullable();
            $table->string('prim_sid', 64)->nullable();
            $table->string('reg_priv', 64)->nullable();
            $table->string('cable_if', 64)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->tableName);
    }
}
