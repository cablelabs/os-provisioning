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

class CreateSnmpMibTable extends BaseMigration
{
    // name of the table to create
    protected $tablename = 'snmpmib';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('snmpmib', function (Blueprint $table) {
            $this->up_table_generic($table);

            $table->integer('devicetype_id')->unsigned();
            $table->enum('html_type', ['text', 'select', 'groupbox', 'textarea']);
            $table->string('html_frame', 16);
            $table->text('html_properties');
            $table->integer('html_id')->unsigned(); // for feature use
            $table->string('field');
            $table->string('oid');
            $table->boolean('oid_table');
            $table->enum('type', ['i', 'u', 's', 'x', 'd', 'n', 'o', 't', 'a', 'b']);
            $table->string('type_array');
            $table->text('phpcode_pre');
            $table->text('phpcode_post');
            $table->text('description');
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
        Schema::drop('snmpmib');
    }
}
