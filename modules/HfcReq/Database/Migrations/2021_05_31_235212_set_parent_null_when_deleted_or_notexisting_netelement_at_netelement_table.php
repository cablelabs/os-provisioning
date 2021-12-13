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

use Modules\HfcReq\Entities\NetElement;

class SetParentNullWhenDeletedOrNotexistingNetelementAtNetelementTable extends BaseMigration
{
    public $migrationScope = 'database';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //Set parent_id null for trashed netelements
        $deleted_netelements = NetElement::onlyTrashed()->get();
        NetElement::whereIn('parent_id', $deleted_netelements->pluck('id'))
            ->whereNotNull('parent_id')
            ->update(['parent_id' => null]);

        // Set parent_id null for non existing netelements
        $netelements = NetElement::all();
        NetElement::whereNotIn('parent_id', $netelements->pluck('id'))
            ->whereNotNull('parent_id')
            ->update(['parent_id' => null]);

        // Fix tree
        NetElement::fixTree();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
