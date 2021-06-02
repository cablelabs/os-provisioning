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

class UpdateParameterAutoincOffset extends Migration
{
    // name of the table to create
    protected $tablename = 'parameter';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // let netelementtype id start with 1000, so that we can add enough upstream netelementtypes <1000,
        // which won't collide with user defined ones
        DB::statement("UPDATE $this->tablename SET netelementtype_id = netelementtype_id + 1000 where netelementtype_id > 6;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
