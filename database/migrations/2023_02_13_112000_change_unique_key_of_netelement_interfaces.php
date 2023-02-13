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
     * Interface index should be more reliable than the name of an interface
     *
     * @return void
     */
    public function up()
    {
        $indexes = Schema::getConnection()->getDoctrineSchemaManager()->listTableIndexes('netelement_interface');

        if (array_key_exists('netelement_interface_netelement_id_mac_if_index_unique', $indexes)) {
            Schema::table($this->tableName, function (Blueprint $table) {
                $table->dropUnique('netelement_interface_netelement_id_mac_if_index_unique');
            });
        }

        if (array_key_exists('netelement_interface_netelement_id_name_unique', $indexes)) {
            Schema::table($this->tableName, function (Blueprint $table) {
                $table->dropUnique('netelement_interface_netelement_id_name_unique');
            });
        }

        Schema::table($this->tableName, function (Blueprint $table) {
            $table->unique(['netelement_id', 'if_index']);
        });

        DB::statement("ALTER TABLE $this->tableName ALTER COLUMN inbound_rate TYPE bigint");
        DB::statement("ALTER TABLE $this->tableName ALTER COLUMN outbound_rate TYPE bigint");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table($this->tableName, function (Blueprint $table) {
            $table->dropUnique('netelement_interface_netelement_id_if_index_unique');
            $table->unique(['netelement_id', 'mac', 'if_index']);
            $table->unique(['netelement_id', 'name']);
        });

        DB::statement("ALTER TABLE $this->tableName ALTER COLUMN inbound_rate TYPE double precision");
        DB::statement("ALTER TABLE $this->tableName ALTER COLUMN outbound_rate TYPE double precision");
    }
};
