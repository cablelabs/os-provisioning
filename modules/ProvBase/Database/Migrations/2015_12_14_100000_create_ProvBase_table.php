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

class CreateProvBaseTable extends BaseMigration
{
    // name of the table to create
    protected $tablename = 'provbase';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tablename, function (Blueprint $table) {
            $this->up_table_generic($table);

            $table->string('provisioning_server');
            $table->string('ro_community');
            $table->string('rw_community');
            $table->string('notif_mail');
            $table->string('domain_name');
            $table->integer('dhcp_def_lease_time')->unsigned();
            $table->integer('dhcp_max_lease_time')->unsigned();
            $table->integer('startid_contract')->unsigned();
            $table->integer('startid_modem')->unsigned();
            $table->integer('startid_endpoint')->unsigned();
        });

        DB::update('INSERT INTO '.$this->tablename." (provisioning_server, ro_community, rw_community, domain_name, dhcp_def_lease_time, dhcp_max_lease_time) VALUES('172.20.0.1', 'public', 'private', 'nmsprime.test', 86400, 172800);");
        // create dhcpd config files
        exec('php /var/www/nmsprime/artisan nms:dhcp');
        exec('chown -R apache:dhcpd /etc/dhcp-nmsprime');

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
