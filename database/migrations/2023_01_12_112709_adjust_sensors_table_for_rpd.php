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

return new class extends BaseMigration
{
    public $migrationScope = 'database';

    protected $tableName = 'sensor';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->tableName, function (Blueprint $table) {
            $table->dropColumn('temperature_id');
            $table->string('name')->nullable()->change();
            $table->string('status')->nullable()->change();
            $table->decimal('value', 12, 2)->nullable()->change();
            $table->string('scale', 25)->after('unit')->nullable();
            $table->integer('precision')->after('scale')->nullable();
            $table->integer('internal_id')->after('core_element_id')->nullable();
            $table->unique(['internal_id', 'core_element_id', 'core_element_type'], 'sensor_internal_id_unique_idx');
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
            $table->integer('temperature_id')->after('unit')->nullable();
            $table->string('name')->change();
            $table->string('status')->change();
            $table->float('value')->change();
            $table->dropColumn([
                'scale',
                'precision',
                'internal_id',
            ]);
            $table->dropIndex('sensor_internal_id_unique_idx');
        });
    }
};
