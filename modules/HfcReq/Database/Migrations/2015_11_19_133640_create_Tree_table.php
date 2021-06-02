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

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTreeTable extends BaseMigration
{
    // name of the table to create
    protected $tablename = 'tree';

    /**
     * Run the migrations - NOTE: This table will be renamed to NetElement in next Migrations
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tablename, function (Blueprint $table) {
            $this->up_table_generic($table);

            $table->string('name');
            $table->integer('series');
            $table->integer('options');
            $table->string('ip');
            $table->string('pos', 45);
            $table->string('link');
            $table->integer('parent_id')->unsigned();
            $table->integer('user');
            $table->integer('access');
            $table->integer('net');				// for fast assignment of Clusters to Net (it's possible to have multiple parents between cluster & net)
            $table->integer('cluster');
            $table->integer('layer');
            $table->text('descr');
            $table->string('kml_file');
            $table->string('draw');
            $table->string('line');

            // droped on next migration - but here for backward compatibility
            $table->string('type');
            $table->integer('type_new')->unsigned();
            $table->string('tp', 8);
            $table->integer('tp_new');
            $table->string('state');
            $table->integer('state_new');
            $table->integer('parent');
        });

        return parent::up();
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
