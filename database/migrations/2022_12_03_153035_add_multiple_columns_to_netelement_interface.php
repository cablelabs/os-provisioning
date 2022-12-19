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
            $table->macAddress('mac')->nullable();
            $table->bigInteger('total_bw')->nullable();
            $table->float('inbound_rate')->nullable();
            $table->float('outbound_rate')->nullable();
            $table->bigInteger('prev_inbound_counter')->nullable();
            $table->bigInteger('prev_outbound_counter')->nullable();
            $table->float('total_util')->nullable();
            $table->float('inbound_util')->nullable();
            $table->float('outbound_util')->nullable();
            $table->float('total_error_ratio')->nullable();
            $table->float('inbound_error_ratio')->nullable();
            $table->float('outbound_error_ratio')->nullable();
            $table->bigInteger('prev_inbound_error_counter')->nullable();
            $table->bigInteger('prev_outbound_error_counter')->nullable();
            $table->unique(['netelement_id', 'name']);
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
                'mac',
                'total_bw',
                'inbound_rate',
                'outbound_rate',
                'prev_inbound_counter',
                'prev_outbound_counter',
                'total_util',
                'inbound_util',
                'outbound_util',
                'total_error_ratio',
                'inbound_error_ratio',
                'outbound_error_ratio',
                'prev_inbound_error_counter',
                'prev_outbound_error_counter',
            ]);
        });
    }
};
