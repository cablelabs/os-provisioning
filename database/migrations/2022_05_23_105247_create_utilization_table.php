<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class CreateUtilizationTable extends BaseMigration
{
    public $migrationScope = 'database';

    protected $tableName = 'utilization';

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
            $table->integer('rpd_id');

            $table->integer('rpd_no')->nullable();
            $table->string('lable_if', 64)->nullable();
            $table->string('ccap_core_name', 64)->nullable();
            $table->string('hub_name', 64)->nullable();
            $table->string('market_name', 64)->nullable();
            $table->string('name_idx', 64)->nullable();
            $table->string('fn_name', 64)->nullable();
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
