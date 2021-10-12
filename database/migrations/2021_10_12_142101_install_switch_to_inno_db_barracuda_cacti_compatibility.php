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
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class InstallSwitchToInnoDbBarracudaCactiCompatibility extends BaseMigration
{
    public $migrationScope = 'database';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $dbCon = DB::connection('mysql-root');

        // Change Characterset of Cacti DB
        $dbCon->statement('ALTER DATABASE cacti CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;');

        // Set global Variables for new Tables inside the Database
        $dbCon->statement('SET GLOBAL innodb_file_format=Barracuda;');
        $dbCon->statement('SET GLOBAL innodb_file_format_max=Barracuda;');
        $dbCon->statement('SET GLOBAL innodb_large_prefix=1;');
        $dbCon->statement('SET GLOBAL innodb_file_per_table=1;');

        // persist variables in mariadb config
        $mariaConf = File::get('/etc/my.cnf.d/server.cnf');

        if (Str::contains($mariaConf, 'innodb_file_format=Barracuda')) {
            $mariaConf = Str::replaceFirst("[mariadb]\n",
                    "[mariadb]\n".
                    "innodb_file_format=Barracuda\n".
                    "innodb_file_format_max=Barracuda\n".
                    "character_set_server=utf8mb4\n".
                    "character_set_client=utf8mb4\n".
                    "innodb_large_prefix=1\n".
                    "innodb_file_per_table=ON\n",
                    $mariaConf);

            File::put('/etc/my.cnf.d/server.cnf', $mariaConf);
        }

        //restart mariadb
        system('systemctl restart mariadb');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
