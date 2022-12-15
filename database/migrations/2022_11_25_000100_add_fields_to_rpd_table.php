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
    protected $tableName = 'rpd';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->tableName, function (Blueprint $table) {
            $table->bigInteger('netelement_ccap_id')->nullable();
            $table->string('state_changed_at')->nullable();         // stateChangeTimestamp in Smartphy response
            $table->unique(['mac', 'netelement_id']);

            if (! Schema::hasColumn($this->tableName, 'cable_if')) {
                $table->string('cable_if')->nullable();
            }
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
            $table->dropColumns(['netelement_ccap_id', 'cable_if', 'state_changed_at']);
            $table->dropUnique(['mac', 'netelement_id']);
        });
    }
};
