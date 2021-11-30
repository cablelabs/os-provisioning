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
class AddPassiveNetelementtypes extends BaseMigration
{
    public $migrationScope = 'database';

    protected $tableName = 'netelementtype';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("INSERT INTO $this->tableName (id, created_at, updated_at, name, base_type) VALUES (11, NOW(), NOW(), 'Passives', 11)");
        DB::statement("INSERT INTO $this->tableName (id, created_at, updated_at, name, parent_id, base_type) VALUES (12, NOW(), NOW(), 'Splitter', 11, 11)");
        DB::statement("INSERT INTO $this->tableName (id, created_at, updated_at, name, parent_id, base_type) VALUES (13, NOW(), NOW(), 'Amplifier', 11, 11)");
        DB::statement("INSERT INTO $this->tableName (id, created_at, updated_at, name, parent_id, base_type) VALUES (14, NOW(), NOW(), 'Node', 11, 11)");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DELETE FROM $this->tableName WHERE id in (11,12,13,14)");
    }
}
