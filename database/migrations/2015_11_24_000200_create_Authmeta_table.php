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

class CreateAuthmetaTable extends BaseMigration
{
    // name of the table to create
    protected $tablename = 'authmetas';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tablename, function (Blueprint $table) {
            $this->up_table_generic($table);

            $table->string('name', 191);
            $table->enum('type', ['role', 'client']);
            $table->string('description');

            $table->unique(['name', 'type']);
        });

        // the following “seeding” is needed in every case – even if the seeders will not be run!
        DB::table($this->tablename)->insert([
            ['id' => 1, 'name'=>'super_admin', 'type'=>'role', 'description'=>'Is allowed to do everything. Used for the initial user which can add other users.'],
            ['id' => 2, 'name'=>'every_net', 'type'=>'client', 'description'=>'Is allowed to access every net. Used for the initial user which can add other users.'],
        ]);
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
