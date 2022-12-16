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
            $table->float('input_rate')->nullable();
            $table->float('output_rate')->nullable();
            $table->bigInteger('prev_input_counter')->nullable();
            $table->bigInteger('prev_output_counter')->nullable();
            $table->float('total_util')->nullable();
            $table->float('input_util')->nullable();
            $table->float('output_util')->nullable();
            $table->float('total_error_ratio')->nullable();
            $table->float('input_error_ratio')->nullable();
            $table->float('output_error_ratio')->nullable();
            $table->bigInteger('prev_input_error_counter')->nullable();
            $table->bigInteger('prev_output_error_counter')->nullable();
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
                'input_rate',
                'output_rate',
                'prev_input_counter',
                'prev_output_counter',
                'total_util',
                'input_util',
                'output_util',
                'total_error_ratio',
                'input_error_ratio',
                'output_error_ratio',
                'prev_input_error_counter',
                'prev_output_error_counter',
            ]);
        });
    }
};
