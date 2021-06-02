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

use Modules\ProvBase\Entities\NetGw;
use Illuminate\Database\Schema\Blueprint;

class CreateCmtsTable extends BaseMigration
{
    // name of the table to create
    protected $tablename = 'cmts';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tablename, function (Blueprint $table) {
            $this->up_table_generic($table);

            $table->string('hostname');
            $table->string('type');
            $table->string('ip');		// bundle ip
            $table->string('community_rw');
            $table->string('community_ro');
            $table->string('company');
            $table->integer('network');
            $table->integer('state');
            $table->integer('monitoring');
        });

        $this->set_fim_fields(['hostname', 'type', 'ip', 'community_ro', 'community_rw', 'company']);

        // add fulltext index for all given fields
        // TODO: remove ?
        if (isset($this->index) && (count($this->index) > 0)) {
            DB::statement('CREATE FULLTEXT INDEX '.$this->tablename.'_all ON '.$this->tablename.' ('.implode(', ', $this->index).')');
        }

        return parent::up();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // empty CMTS includes
        file_put_contents(NetGw::NETGW_INCLUDE_PATH.'.conf', '');

        Schema::drop($this->tablename);

        // remove all through dhcpCommand created cmts config files
        foreach (glob(NetGw::NETGW_INCLUDE_PATH.'/*') as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }
}
