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

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class CreateAlarmTable extends BaseMigration
{
    public $migrationScope = 'database';

    protected $tableName = 'alarm';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $this->up_table_generic($table);
            $table->string('status')->nullable();
            $table->string('agent_host')->nullable();
            $table->string('alertname')->nullable();
            $table->string('host')->nullable();
            $table->string('instance')->nullable();
            $table->string('job')->nullable();
            $table->string('severity')->nullable();
            $table->json('annotations')->nullable();
            $table->timestamp('startsAt', null)->nullable();
            $table->timestamp('endsAt', null)->nullable();
            $table->string('generatorURL')->nullable();
            $table->string('fingerprint')->nullable();
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
