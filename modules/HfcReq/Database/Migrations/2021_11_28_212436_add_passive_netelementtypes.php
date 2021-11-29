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
use Modules\HfcReq\Entities\NetElementType;

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
        DB::statement("INSERT INTO $this->tableName (id, created_at, updated_at, name, base_type) VALUES (10, NOW(), NOW(), 'Passives', 10)");
        DB::statement("INSERT INTO $this->tableName (id, created_at, updated_at, name, parent_id, base_type) VALUES (11, NOW(), NOW(), 'Splitter', 10, 10)");
        DB::statement("INSERT INTO $this->tableName (id, created_at, updated_at, name, parent_id, base_type) VALUES (12, NOW(), NOW(), 'Amplifier', 10, 10)");
        DB::statement("INSERT INTO $this->tableName (id, created_at, updated_at, name, parent_id, base_type) VALUES (13, NOW(), NOW(), 'Node', 10, 10)");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DELETE FROM $this->tableName WHERE id in (10,11,12,13)");
    }
}
