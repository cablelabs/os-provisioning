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

class CreateLinkTable extends BaseMigration
{
    public $migrationScope = 'database';
    protected $tablename = 'link';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tablename, function (Blueprint $table) {
            $this->up_table_generic($table);

            // Netelement IDs
            $table->integer('from')->unsigned();
            $table->integer('to')->unsigned();

            $table->string('name')->nullable();
            $table->string('if_from')->nullable();
            $table->string('if_to')->nullable();
            $table->string('type')->nullable();
            $table->string('state')->nullable();
            $table->text('description')->nullable();
        });

        // Create Link for each parent-child relationship
        if (Module::collections()->has('CoreMon')) {
            $netelements = \Modules\HfcReq\Entities\NetElement::get()->toTree();

            $this->createLinks($netelements);
        }
    }

    private function createLinks($netelements)
    {
        foreach ($netelements as $ne) {
            if ($ne->parent_id) {
                $ne->createLink();
            }

            if ($ne->children->isEmpty()) {
                continue;
            }

            foreach ($ne->children as $child) {
                $child->setRelation('parent', $ne);
            }

            $this->createLinks($ne->children);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop($this->tablename);
    }
}
