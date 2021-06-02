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

use Illuminate\Database\Migrations\Migration;

class UpdateContractVoipIdNullable extends Migration
{
    // name of the table to create
    protected $tablename = 'contract';

    /**
     * Run the migrations.
     * For using the envia TEL API we need some changes in storing the contracts data.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE '.$this->tablename.' MODIFY voip_id INTEGER UNSIGNED NULL DEFAULT NULL;');
        DB::statement('ALTER TABLE '.$this->tablename.' MODIFY next_voip_id INTEGER UNSIGNED NULL DEFAULT NULL;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('UPDATE '.$this->tablename.' SET voip_id = 0 WHERE voip_id is NULL;');
        DB::statement('UPDATE '.$this->tablename.' SET next_voip_id = 0 WHERE next_voip_id is NULL;');

        DB::statement('ALTER TABLE '.$this->tablename.' MODIFY voip_id INTEGER UNSIGNED NOT NULL;');
        DB::statement('ALTER TABLE '.$this->tablename.' MODIFY next_voip_id INTEGER UNSIGNED NOT NULL;');
    }
}
