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

use DB;
use Illuminate\Database\Schema\Blueprint;

class InstallInitRadiusAndAcs extends BaseMigration
{
    public $migrationScope = 'database';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Use schema from git, since it adds the id column in radusergroup
        DB::connection('pgsql-radius')->unprepared(file_get_contents('https://raw.githubusercontent.com/FreeRADIUS/freeradius-server/b838f5178fe092598fb3459dedb5e1ea49b41340/raddb/mods-config/sql/main/postgresql/schema.sql'));
        \Artisan::call('nms:radgroupreply-repopulate');

        $config = DB::connection('pgsql-radius')->getConfig();

        $find = [
            '/^\s*#*\s*driver\s*=.*/m',
            '/^\s*#*\s*dialect\s*=.*/m',
            '/^\s*#*\s*login\s*=.*/m',
            '/^\s*#*\s*password\s*=.*/m',
            '/^\s*radius_db\s*=.*/m',
            '/^\s*#*\s*read_clients\s*=.*/m',
        ];

        $replace = [
            "\tdriver = \"rlm_sql_postgresql\"",
            "\tdialect = \"postgresql\"",
            "\tlogin = \"{$config['username']}\"",
            "\tpassword = \"{$config['password']}\"",
            "\tradius_db = \"{$config['database']}\"",
            "\tread_clients = yes",
        ];

        $filename = '/etc/raddb/mods-available/sql';
        $content = file_get_contents($filename);
        $content = preg_replace($find, $replace, $content);
        file_put_contents($filename, $content);

        $link = '/etc/raddb/mods-enabled/sql';
        symlink('/etc/raddb/mods-available/sql', $link);
        // We can't use php chrgp, since it always dereferences symbolic links
        exec("chgrp -h radiusd $link");

        // Add sqlippool table
        DB::connection('pgsql-radius')->unprepared(file_get_contents('/etc/raddb/mods-config/sql/ippool/postgresql/schema.sql'));

        // Adjust radiusd sqlippool IP lease duration
        $leaseTime = Modules\ProvBase\Entities\ProvBase::first()->dhcp_def_lease_time;
        $queryPath = storage_path('app/config/provbase/radius/queries.conf');
        exec("sed -i -e 's/^\s*lease_duration\s*=.*/\tlease_duration = $leaseTime/' -e '/^\s*\$INCLUDE/i\\\\t\$INCLUDE $queryPath' /etc/raddb/mods-available/sqlippool");

        // Enable sqlippool
        $link = '/etc/raddb/mods-enabled/sqlippool';
        exec("ln -srf /etc/raddb/mods-available/sqlippool $link");
        exec("chgrp -h radiusd $link");
        exec("sed -i -e '/^accounting {/a\\\\tsqlippool' -e '/^post-auth {/a\\\\tsqlippool' /etc/raddb/sites-enabled/default");

        // Disable RADIUS detail logging
        exec("sed -i 's/^\s*detail/#\tdetail/' /etc/raddb/sites-enabled/default");

        exec('systemctl restart radiusd.service');

        // Enable and Start Genie-ACS
        foreach (['mongod', 'genieacs-cwmp', 'genieacs-fs', 'genieacs-nbi', 'genieacs-ui'] as $service) {
            exec("systemctl enable $service.service");
            exec("systemctl start $service.service");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $radiusTables = ['radacct', 'radcheck', 'radgroupcheck', 'radgroupreply', 'radreply', 'radusergroup', 'radpostauth', 'nas'];

        foreach ($radiusTables as $table) {
            Schema::drop($table);
        }
    }
}
