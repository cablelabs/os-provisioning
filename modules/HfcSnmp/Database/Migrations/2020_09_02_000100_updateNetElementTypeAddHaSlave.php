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

use Modules\HfcReq\Entities\NetElementType;
use Illuminate\Database\Migrations\Migration;

class UpdateNetElementTypeAddHaSlave extends Migration
{
    protected $tablename = 'netelementtype';

    /**
     * Run the migrations.
     *
     * @author Patrick Reichel, adapted from Nino
     * @return void
     */
    public function up()
    {
        // Make sure that netelementtype ID 10 is free
        $id = 9;
        do {
            $id++;

            $exists = NetElementType::find($id);
        } while ($exists);

        if ($id != 10) {
            DB::statement("UPDATE $this->tablename SET id=$id WHERE $id=10");
            DB::statement("UPDATE netelement SET netelementtype_id=$id WHERE netelementtype_id=10");
        }

        DB::statement("INSERT INTO $this->tablename (id, created_at, updated_at, name) VALUES (10, NOW(), NOW(), 'NMSPrime HA slave')");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DELETE FROM $this->tablename WHERE id=10");
    }
}
