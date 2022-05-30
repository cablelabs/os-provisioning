<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class AdditionalRpdColumns extends BaseMigration
{
    public $migrationScope = 'system';

    protected $tableName = 'rpds';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->tableName, function (Blueprint $table) {
            $table->string('fiber_node_name')->nullable();
            $table->string('bc_vid_oob_if')->nullable();
            $table->string('nc_vid_oob_if')->nullable();
            $table->string('ds_data_cont_profile')->nullable();
            $table->string('us_data_cont_profile')->nullable();
            $table->string('cin_rpa')->nullable();
            $table->string('cin_dpa')->nullable();
            $table->string('cm_no_service_group')->nullable();
            $table->string('dpa')->nullable();
            $table->string('rpa1')->nullable();
            $table->string('rpa2')->nullable();
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
            $table->dropColumn('fiber_node_name');
            $table->dropColumn('bc_vid_oob_if');
            $table->dropColumn('nc_vid_oob_if');
            $table->dropColumn('ds_data_cont_profile');
            $table->dropColumn('us_data_cont_profile');
            $table->dropColumn('cin_rpa');
            $table->dropColumn('cin_dpa');
            $table->dropColumn('cm_no_service_group');
            $table->dropColumn('dpa');
            $table->dropColumn('rpa1');
            $table->dropColumn('rpa2');
        });
    }
}
