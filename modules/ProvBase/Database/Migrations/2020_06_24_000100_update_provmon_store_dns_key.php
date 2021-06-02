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

use Modules\ProvBase\Entities\ProvBase;
use Illuminate\Database\Schema\Blueprint;

class UpdateProvmonStoreDnsKey extends BaseMigration
{
    protected $tablename = 'provbase';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        // create column to store DNS update key
        Schema::table($this->tablename, function (Blueprint $table) {
            $table->string('dns_password')->after('domain_name');
        });

        $dns_password = 'n/a';
        // get the current DNS password
        if (preg_match('/secret +"?(.*)"?;/', file_get_contents('/etc/named-nmsprime.conf'), $matches)) {
            $dns_password = str_replace('"', '', $matches[1]);
        }

        // or create a new one
        if (
            (substr($dns_password, -1) != '=') &&
            (preg_match('/secret "?(.*)"?;/', shell_exec('ddns-confgen -a hmac-md5 -r /dev/urandom | grep secret'), $matches))
        ) {
            $dns_password = str_replace('"', '', $matches[1]);
        }

        // or at least give a hint
        if (substr($dns_password, -1) != '=') {
            $dns_password = 'to be set';
        }

        // store in database
        $provha = ProvBase::first();
        $provha->dns_password = $dns_password;
        $provha->save();

        // remove from .env file to avoid confusion
        $conf = file_get_contents('/etc/nmsprime/env/global.env');
        $conf = preg_replace('/\nDNS_PASSWORD=.*$/m', '', $conf);
        file_put_contents('/etc/nmsprime/env/global.env', $conf);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table($this->tablename, function (Blueprint $table) {
            $table->dropColumn([
                'dns_password',
            ]);
        });
    }
}
