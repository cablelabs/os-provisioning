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

class RenameCmtsToNetGw extends BaseMigration
{
    protected $tablename = 'netgw';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('cmts', $this->tablename);

        Schema::table($this->tablename, function (Blueprint $table) {
            $table->renameColumn('type', 'series');
        });

        Schema::table($this->tablename, function (Blueprint $table) {
            $table->string('type')->default('cmts');
        });

        Schema::table('ippool', function (Blueprint $table) {
            $table->renameColumn('cmts_id', 'netgw_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table($this->tablename, function (Blueprint $table) {
            $table->dropColumn('type');
        });

        Schema::table($this->tablename, function (Blueprint $table) {
            $table->renameColumn('series', 'type');
        });

        Schema::rename($this->tablename, 'cmts');

        Schema::table('ippool', function (Blueprint $table) {
            $table->renameColumn('netgw_id', 'cmts_id');
        });
    }
}
