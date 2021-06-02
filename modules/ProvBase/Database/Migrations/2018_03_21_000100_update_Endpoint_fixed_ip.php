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

class UpdateEndpointFixedIp extends BaseMigration
{
    // name of the table to create
    protected $tablename = 'endpoint';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->tablename, function (Blueprint $table) {
            $table->integer('modem_id')->unsigned()->nullable();
            $table->string('ip', 15)->nullable();
            $table->dropColumn('type');
            $table->dropColumn('name');
        });

        DB::statement("ALTER TABLE $this->tablename MODIFY hostname varchar(63)");
        DB::statement("ALTER TABLE $this->tablename CHANGE public fixed_ip tinyint(1)");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table($this->tablename, function (Blueprint $table) {
            $table->dropColumn(['modem_id', 'ip']);
            $table->enum('type', ['cpe', 'mta'])->nullable();
            $table->string('name')->nullable();
        });

        DB::statement("ALTER TABLE $this->tablename MODIFY hostname varchar(255)");
        DB::statement("ALTER TABLE $this->tablename CHANGE fixed_ip public tinyint(1)");
    }
}
