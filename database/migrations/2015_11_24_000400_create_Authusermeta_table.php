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

class CreateAuthusermetaTable extends BaseMigration
{
    // name of the table to create
    protected $tablename = 'authusermeta';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tablename, function (Blueprint $table) {
            $this->up_table_generic($table);

            $table->integer('user_id')->unsigned();
            $table->integer('meta_id')->unsigned();

            $table->foreign('user_id')->references('id')->on('authuser');
            $table->foreign('meta_id')->references('id')->on('authmeta');

            $table->unique(['user_id', 'meta_id']);
        });

        // the following “seeding” is needed in every case – even if the seeders will not be run!
        DB::update('INSERT INTO '.$this->tablename.' (user_id, meta_id) VALUES(1, 1);');
        DB::update('INSERT INTO '.$this->tablename.' (user_id, meta_id) VALUES(1, 2);');
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
