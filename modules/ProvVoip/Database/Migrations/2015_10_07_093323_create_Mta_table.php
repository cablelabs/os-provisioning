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

class CreateMtaTable extends BaseMigration
{
    // name of the table to create
    protected $tablename = 'mta';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // creates directory for mta config files and changes owner
        $dir = '/tftpboot/mta';
        if (! is_dir($dir)) {
            mkdir($dir, '0755');
        }
        system('/bin/chown -R apache /tftpboot/mta');

        Schema::create($this->tablename, function (Blueprint $table) {
            $this->up_table_generic($table);

            $table->integer('modem_id')->unsigned()->default(1);
            $table->string('mac', 17);
            $table->string('hostname');
            $table->integer('configfile_id')->unsigned()->default(1);
            $table->enum('type', ['sip', 'packetcable']);
            $table->boolean('is_dummy')->default(0);
        });

        foreach ([1 => 'sip', 2 => 'packetcable'] as $i => $v) {
            DB::update('INSERT INTO '.$this->tablename." (hostname, type, is_dummy, deleted_at) VALUES('dummy-mta-".$v."',".$i.',1,NOW());');
        }

        $this->set_fim_fields(['mac', 'hostname']);
        $this->set_auto_increment(300000);

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
