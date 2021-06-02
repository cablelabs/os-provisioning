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
use Modules\HfcReq\Entities\NetElementType;

class RenameDeviceTypetoNetElementTypeTable extends BaseMigration
{
    // name of the table to create
    protected $tablename = 'netelementtype';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('devicetype', $this->tablename);

        Schema::table($this->tablename, function (Blueprint $table) {
            $table->string('icon_name');
            $table->integer('pre_conf_oid_id');
            $table->string('pre_conf_value');
            $table->integer('pre_conf_time_offset'); 		// in microsec
            $table->float('page_reload_time'); 				// in sec
        });

        // Set Default Entries
        $defaults = ['Net', ['Cluster', 1], 'Cmts', 'Amplifier', 'Node', 'Data'];

        // delete entries first
        NetElementType::truncate();

        foreach ($defaults as $d) {
            is_array($d) ? NetElementType::create(['name' => $d[0], 'parent_id' => $d[1]]) : NetElementType::create(['name' => $d]);
        }

        $this->set_fim_fields(['name', 'vendor', 'description']);

        return parent::up();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table($this->tablename, function (Blueprint $table) {
            $table->dropColumn('icon_name');
        });

        Schema::rename($this->tablename, 'devicetype');
    }
}
