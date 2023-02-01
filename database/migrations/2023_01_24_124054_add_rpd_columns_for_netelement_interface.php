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
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

return new class extends BaseMigration
{
    public $migrationScope = 'database';
    protected $tableName = 'netelement_interface';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->tableName, function (Blueprint $table) {
            $table->bigInteger('inbound_frame_rate')->nullable();
            $table->bigInteger('outbound_frame_rate')->nullable();
            $table->bigInteger('inbound_unicast_rate')->nullable();
            $table->bigInteger('outbound_unicast_rate')->nullable();
            $table->bigInteger('inbound_unicast_frame_rate')->nullable();
            $table->bigInteger('outbound_unicast_frame_rate')->nullable();
            $table->bigInteger('inbound_multicast_rate')->nullable();
            $table->bigInteger('outbound_multicast_rate')->nullable();
            $table->bigInteger('inbound_multicast_frame_rate')->nullable();
            $table->bigInteger('outbound_multicast_frame_rate')->nullable();
            $table->bigInteger('inbound_broadcast_rate')->nullable();
            $table->bigInteger('outbound_broadcast_rate')->nullable();
            $table->bigInteger('inbound_broadcast_frame_rate')->nullable();
            $table->bigInteger('outbound_broadcast_frame_rate')->nullable();
            $table->bigInteger('inbound_discard_rate')->nullable();
            $table->bigInteger('outbound_discard_rate')->nullable();

            $table->bigInteger('prev_inbound_frame_counter')->nullable();
            $table->bigInteger('prev_outbound_frame_counter')->nullable();
            $table->bigInteger('prev_inbound_unicast_counter')->nullable();
            $table->bigInteger('prev_outbound_unicast_counter')->nullable();
            $table->bigInteger('prev_inbound_unicast_frame_counter')->nullable();
            $table->bigInteger('prev_outbound_unicast_frame_counter')->nullable();
            $table->bigInteger('prev_inbound_multicast_counter')->nullable();
            $table->bigInteger('prev_outbound_multicast_counter')->nullable();
            $table->bigInteger('prev_inbound_multicast_frame_counter')->nullable();
            $table->bigInteger('prev_outbound_multicast_frame_counter')->nullable();
            $table->bigInteger('prev_inbound_broadcast_counter')->nullable();
            $table->bigInteger('prev_outbound_broadcast_counter')->nullable();
            $table->bigInteger('prev_inbound_broadcast_frame_counter')->nullable();
            $table->bigInteger('prev_outbound_broadcast_frame_counter')->nullable();
            $table->bigInteger('prev_inbound_discard_counter')->nullable();
            $table->bigInteger('prev_outbound_discard_counter')->nullable();
            $table->unique(['netelement_id', 'mac']);
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
            $table->dropColumn([
                'inbound_frame_rate',
                'outbound_frame_rate',
                'inbound_unicast_rate',
                'outbound_unicast_rate',
                'inbound_unicast_frame_rate',
                'outbound_unicast_frame_rate',
                'inbound_multicast_rate',
                'outbound_multicast_rate',
                'inbound_multicast_frame_rate',
                'outbound_multicast_frame_rate',
                'inbound_broadcast_rate',
                'outbound_broadcast_rate',
                'inbound_broadcast_frame_rate',
                'outbound_broadcast_frame_rate',
                'inbound_discard_rate',
                'outbound_discard_rate',
                'prev_inbound_frame_counter',
                'prev_outbound_frame_counter',
                'prev_inbound_unicast_counter',
                'prev_outbound_unicast_counter',
                'prev_inbound_unicast_frame_counter',
                'prev_outbound_unicast_frame_counter',
                'prev_inbound_multicast_counter',
                'prev_outbound_multicast_counter',
                'prev_inbound_multicast_frame_counter',
                'prev_outbound_multicast_frame_counter',
                'prev_inbound_broadcast_counter',
                'prev_outbound_broadcast_counter',
                'prev_inbound_broadcast_frame_counter',
                'prev_outbound_broadcast_frame_counter',
                'prev_inbound_discard_counter',
                'prev_outbound_discard_counter',
            ]);

            $table->dropUnique('netelement_interface_netelement_id_mac_unique');
        });
    }
};
