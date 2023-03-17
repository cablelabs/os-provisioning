<?php
/**
 * Copyright (c) NMS PRIME GmbH ("NMS PRIME Community Version")
 * and others – powered by CableLabs. All rights reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at:
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

use Database\Migrations\BaseMigration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRpdTable extends BaseMigration
{
    public $migrationScope = 'database';

    protected $tableName = 'rpd';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * off = offline; pct = percentage; part = partial; dg = downgrade;
         * nc = narrowcast; bc = broadcast; cont = controller
         */
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestampsTz(null);
            $table->softDeletesTz('deleted_at', null);
            $table->integer('netelement_id');

            // RPD infos
            $table->string('name', 64)->nullable();
            $table->string('mac', 17)->nullable();
            $table->boolean('aux')->nullable();
            $table->string('uptime')->nullable();
            $table->string('type', 64)->nullable();
            $table->string('vendor', 64)->nullable();
            $table->string('serial_num', 64)->nullable();
            $table->string('model', 64)->nullable();
            $table->string('sw_ver', 64)->nullable();
            $table->string('ccap_if', 64)->nullable();
            $table->string('status', 64)->nullable();
            $table->string('city', 64)->nullable();
            $table->string('site', 64)->nullable();

            // $table->decimal('temp', 6, 3)->nullable();
            // $table->integer('cores_no')->nullable();
            // $table->string('remote_cores_conn', 64)->nullable();
            // $table->string('cores_conn', 64)->nullable();
            // $table->integer('additional_cores')->nullable();
            // $table->integer('dest_hop')->nullable();
            // $table->string('service_template', 64)->nullable();
            // $table->string('service_template_status', 64)->nullable();
            // $table->string('out_of_band_if', 64)->nullable();
            // $table->string('fiber_node_id', 64)->nullable();
            // $table->string('cable_if', 64)->nullable();

            // Statistics
            // $table->integer('cm_no')->nullable();
            // $table->integer('cm_off_no')->nullable();
            // $table->decimal('cm_off_pct', 5, 2)->nullable();
            // $table->integer('mta_no')->nullable();
            // $table->decimal('mta_off_pct', 5, 2)->nullable();
            // $table->integer('dsg_no')->nullable();
            // $table->decimal('dsg_off_pct', 5, 2)->nullable();

            // $table->decimal('cm_ds_part_service_pct', 5, 2)->nullable();
            // $table->integer('cm_ds_part_service_no')->nullable();
            // $table->decimal('cm_us_part_service_pct', 5, 2)->nullable();
            // $table->integer('cm_us_part_service_no')->nullable();

            // $table->decimal('cwerr_oos_pct', 5, 2)->nullable();
            // $table->decimal('snr_oos_pct', 5, 2)->nullable();

            // $table->decimal('cm_tx_lvl_oos_pct', 5, 2)->nullable();
            // $table->decimal('cm_us_snr_oos_pct', 5, 2)->nullable();
            // $table->integer('cm_us_snr_oos_no')->nullable();
            // $table->decimal('cm_us_time_offset_oos_pct', 5, 2)->nullable();
            // $table->integer('cm_us_time_offset_oos_no')->nullable();
            // $table->decimal('cm_us_pow_oos_pct', 5, 2)->nullable();
            // $table->integer('cm_us_pow_oos_no')->nullable();
            // $table->decimal('cm_us_post_fec_oos_pct', 5, 2)->nullable();
            // $table->integer('cm_us_post_fec_oos_no')->nullable();

            // $table->integer('cm_ofdm_ch_1_profile_0_no')->nullable();
            // $table->integer('cm_ofdm_ch_1_profile_1_no')->nullable();
            // $table->integer('cm_ofdm_ch_1_profile_2_no')->nullable();
            // $table->integer('cm_ofdm_ch_1_profile_3_no')->nullable();
            // $table->integer('cm_ofdm_ch_2_profile_0_no')->nullable();
            // $table->integer('cm_ofdm_ch_2_profile_1_no')->nullable();
            // $table->integer('cm_ofdm_ch_2_profile_2_no')->nullable();
            // $table->integer('cm_ofdm_ch_2_profile_3_no')->nullable();
            // $table->integer('cm_ofdm_ch_1_profile_0_pct')->nullable();
            // $table->integer('cm_ofdm_ch_1_profile_1_pct')->nullable();
            // $table->integer('cm_ofdm_ch_1_profile_2_pct')->nullable();
            // $table->integer('cm_ofdm_ch_1_profile_3_pct')->nullable();
            // $table->integer('cm_ofdm_ch_2_profile_0_pct')->nullable();
            // $table->integer('cm_ofdm_ch_2_profile_1_pct')->nullable();
            // $table->integer('cm_ofdm_ch_2_profile_2_pct')->nullable();
            // $table->integer('cm_ofdm_ch_2_profile_3_pct')->nullable();
            // $table->integer('cm_d31_no')->nullable();
            // $table->integer('cm_ofdm_active_no')->nullable();
            // $table->decimal('cm_ofdm_active_pct', 5, 2)->nullable();
            // $table->integer('cm_ofdm_part_no')->nullable();
            // $table->decimal('cm_ofdm_part_pct', 5, 2)->nullable();
            // $table->integer('cm_ofdm_profile_dg_no')->nullable();
            // $table->decimal('cm_ofdm_profile_dg_pct', 5, 2)->nullable();

            // $table->decimal('time_offset_pct', 5, 2)->nullable();

            // $table->decimal('util_ds_scqam', 5, 2)->nullable();
            // $table->decimal('util_us_atdma', 5, 2)->nullable();
            // $table->decimal('util_ds_qam', 5, 2)->nullable();
            // $table->decimal('util_us_qam', 5, 2)->nullable();
            // $table->decimal('util_ds_ofdm', 5, 2)->nullable();
            // $table->decimal('util_us_ofdm', 5, 2)->nullable();
            // $table->decimal('util_ofdma', 5, 2)->nullable();
            // $table->decimal('util_ofdm_ch_1', 5, 2)->nullable();
            // $table->decimal('util_dest_if_in', 5, 2)->nullable();
            // $table->decimal('util_dest_if_out', 5, 2)->nullable();

            // $table->integer('l2tpv3_sess_err')->nullable();
            // $table->integer('l2tpv3_sess_err_pct')->nullable();

            // $table->string('nc_vid_if', 64)->nullable();
            // $table->string('nc_vid_service_grp', 64)->nullable();
            // $table->string('nc_vid_cont', 64)->nullable();
            // $table->string('nc_vid_depi', 64)->nullable();
            // $table->string('nc_vid_cont_profile', 64)->nullable();
            // $table->string('bc_vid_if', 64)->nullable();
            // $table->string('bc_vid_service_grp', 64)->nullable();
            // $table->string('bc_vid_cont', 64)->nullable();
            // $table->string('bc_vid_depi', 64)->nullable();
            // $table->string('bc_vid_cont_profile', 64)->nullable();
            // $table->string('oob_vid_if', 64)->nullable();

            // $table->integer('us_avg_post_fec')->nullable();
            // $table->integer('us_part_service_no')->nullable();
            // $table->integer('us_part_service_pct')->nullable();
            // $table->integer('us_avg_pow')->nullable();
            // $table->integer('us_avg_snr')->nullable();
            // $table->string('ds_data_service_grp', 64)->nullable();
            // $table->string('ds_data_cont', 64)->nullable();
            // $table->string('ds_data_depi', 64)->nullable();
            // $table->string('us_data_service_grp', 64)->nullable();
            // $table->string('us_data_cont', 64)->nullable();

            // returns oid table, e.g. docsRphyRpdIfPhysEntSensorTable
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop($this->tableName);
    }
}
