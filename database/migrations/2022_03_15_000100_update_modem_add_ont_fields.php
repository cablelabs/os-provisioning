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

class UpdateModemAddOntFields extends BaseMigration
{
    public $migrationScope = 'database';

    protected $tableName = 'modem';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->tableName, function (Blueprint $table) {
            $table->unsignedInteger('netgw_id')->nullable();
            $table->unsignedSmallInteger('frame_id')->nullable();
            $table->unsignedSmallInteger('slot_id')->nullable();
            $table->unsignedSmallInteger('port_id')->nullable();
            $table->unsignedInteger('service_port_id')->nullable();
            $table->string('or_id')->nullable();
        });
        // ->change() is not working with Postgres
        DB::statement('ALTER TABLE '.$this->tableName.' ALTER COLUMN ont_id TYPE smallint USING (ont_id)::smallint');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table($this->tableName, function (Blueprint $table) {
            $table->dropColumn([
                'netgw_id',
                'frame_id',
                'slot_id',
                'port_id',
                'ont_id',
                'service_port_id',
                'or_id',
            ]);
        });
    }
}
