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
class InstallDhcpdRename extends BaseMigration
{
    protected $tablename = '';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $old = '/etc/dhcp/nmsprime';
        $new = '/etc/dhcp-nmsprime';

        // install from git
        if (! file_exists($new)) {
            mkdir($new, 0750, true);
            rename("$old/log.conf", "$new/log.conf");
        }

        if (! file_exists("$new/cmts_gws")) {
            mkdir("$new/cmts_gws", 0750, true);
        }

        // move dhcp config to new folder
        // could be either dhcpd.conf or dhcpd.conf.rpmsave
        $files = glob("$old/dhcpd.conf*");
        if (count($files) == 1) {
            rename($files[0], "$new/dhcpd.conf");
            system("sed -i 's|dhcp/nmsprime|dhcp-nmsprime|' $new/dhcpd.conf");
        }

        system("chown -R apache:dhcpd $new");

        // remove old folder
        exec("rm -rf $old");

        echo "NOTICE: execute 'cd /var/www/nmsprime/; php artisan nms:dhcp' after installation!\n";

        // reload systemd because path-dhcpd.conf was changed
        system('systemctl daemon-reload');

        // restart dhcpd
        system('systemctl restart dhcpd.service');
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
