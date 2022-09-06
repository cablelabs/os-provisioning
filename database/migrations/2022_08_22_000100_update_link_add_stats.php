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

class UpdateLinkAddStats extends BaseMigration
{
    public $migrationScope = 'database';
    protected $tablename = 'link';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->tablename, function (Blueprint $table) {
            $table->boolean('is_up_from')->nullable();
            $table->boolean('is_up_to')->nullable();
            $table->integer('speed_from')->nullable();
            $table->integer('speed_to')->nullable();
            $table->float('utilization_from')->nullable();
            $table->float('utilization_to')->nullable();
            $table->integer('rx_errors_from')->nullable();
            $table->integer('rx_errors_to')->nullable();
            $table->integer('tx_errors_from')->nullable();
            $table->integer('tx_errors_to')->nullable();
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
            $table->dropColumn([
                'is_up_from',
                'is_up_to',
                'speed_from',
                'speed_to',
                'utilization_from',
                'utilization_to',
                'rx_errors_from',
                'rx_errors_to',
                'tx_errors_from',
                'tx_errors_to',
            ]);
        });
    }
}
