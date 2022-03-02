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

class AdaptProvBaseDirectoryStructure extends BaseMigration
{
    public $migrationScope = 'system';

    /**
     * Use this migration to create directory structure for ProvBase after squashing migrations
     *
     * @return void
     */
    public function up()
    {
        $dirs = [
            '/tftpboot/cm',
            '/tftpboot/fw',
        ];

        foreach ($dirs as $dir) {
            if (! is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
        }

        system('/bin/chown -R apache /tftpboot/');

        exec('chown -R apache:dhcpd /etc/dhcp-nmsprime');
        exec('php /var/www/nmsprime/artisan nms:dhcp');

        // only necessary for git installations
        if (! is_dir('/var/log/nmsprime')) {
            mkdir('/var/log/nmsprime', 0755);
            chown('/var/log/nmsprime', 'apache');
            chgrp('/var/log/nmsprime', 'apache');
        }

        chmod('/var/log/nmsprime/tftpd-cm.log', 0600);
        chown('/var/log/nmsprime/tftpd-cm.log', 'apache');
        chgrp('/var/log/nmsprime/tftpd-cm.log', 'apache');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
