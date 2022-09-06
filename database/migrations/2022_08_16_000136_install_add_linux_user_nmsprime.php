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

class InstallAddLinuxUserNmsprime extends BaseMigration
{
    public $migrationScope = 'system';

    /**
     * Run the migrations.
     *
     * Add new linux user 'nmsprime' to use as group for files where multiple users/processes
     * need access to - assign apache and telegraf user to the group
     *
     * @return void
     */
    public function up()
    {
        system('useradd nmsprime');
        system('chgrp nmsprime /etc/nmsprime/env/ /etc/nmsprime/env/global.env');
        system('usermod -a -G nmsprime apache');
        system('usermod -a -G nmsprime telegraf');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        system('userdel nmsprime');
        system('groupdel nmsprime');
        system('chgrp apache /etc/nmsprime/env/ /etc/nmsprime/env/global.env');
    }
}
