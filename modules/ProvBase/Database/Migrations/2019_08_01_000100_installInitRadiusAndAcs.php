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

class InstallInitRadiusAndAcs extends BaseMigration
{
    protected $tablename = 'modem';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->tablename, function (Blueprint $table) {
            // align with freeradius DB
            $table->string('ppp_username', 64)->nullable();
            // nmsprime default
            $table->string('ppp_password', 191)->nullable();
        });

        // use schema from git, since it adds the id column in radusergroup
        \DB::unprepared(file_get_contents('https://github.com/FreeRADIUS/freeradius-server/blob/b838f5178fe092598fb3459dedb5e1ea49b41340/raddb/mods-config/sql/main/postgresql/schema.sql'));
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
        // we can't user php chrgp, since it always dereferences symbolic links
        exec("chgrp -h radiusd $link");

        foreach (['radiusd', 'mongod', 'genieacs-cwmp', 'genieacs-fs', 'genieacs-nbi', 'genieacs-ui'] as $service) {
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
