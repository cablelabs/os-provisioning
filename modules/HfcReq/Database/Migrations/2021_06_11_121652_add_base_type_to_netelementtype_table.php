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

class AddBaseTypeToNetelementtypeTable extends BaseMigration
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
        Schema::table($this->tableName, function (Blueprint $table) {
            $table->unsignedBigInteger('base_type');
        });

        foreach (NetElementType::withTrashed()->get() as $netElementType) {
            $p = $netElementType;

            while ($p->parent_id && ! in_array($p->id, [2, 9])) {
                if (! $p->parent) {
                    NetElementType::where('id', $p->id)->update(['base_type' => null]);

                    break;
                }

                $p = $p->parent;
            }

            $netElementType->base_type = $p->id;
            $netElementType->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table($this->tableName, function (Blueprint $table) {
            $table->dropColumn('base_type');
        });
    }
}
