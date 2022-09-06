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
use Modules\HfcReq\Entities\NetElementType;

class InsertNetelementtypesForCoreMon extends BaseMigration
{
    public $migrationScope = 'database';
    protected $tablename = 'netelementtype';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $netelementtypes = [
            'Market',
            'Hubsite',
            'CCAP Core',
            'DPA',
            'NCS',
            'RPA',
            'RPD',
            'CPE',
        ];

        $id = 15;
        $pos = 1;

        NetElementType::where('id', 1)->update(['sidebar_pos' => 1]);

        foreach ($netelementtypes as $name) {
            $id++;
            $pos++;
            DB::statement("INSERT INTO $this->tablename (id, created_at, updated_at, name, sidebar_pos, base_type_id) VALUES ($id, current_timestamp(0), current_timestamp(0), '$name', $pos, $id)");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        NetElementType::whereBetween('id', [16, 23])->forceDelete();
    }
}
