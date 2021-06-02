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

use Illuminate\Database\Schema\Blueprint;

class CreateSlaTable extends BaseMigration
{
    // name of the table to create
    protected $tablename = 'sla';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tablename, function (Blueprint $table) {
            $this->up_table_generic($table);
            $table->string('name')->nullable();
            $table->string('license')->nullable();
            // $table->mediumInteger('num_cmts')->nullable();
            // $table->integer('num_contracts')->nullable();   // TV customers ??
            // $table->integer('num_modems')->nullable();
            // $table->integer('num_netelements')->nullable();
            // $table->string('system_status')->nullable();
        });

        DB::table($this->tablename)->insert(['name' => null]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop($this->tablename);
    }
}
