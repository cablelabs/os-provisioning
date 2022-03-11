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
class InstallKea extends BaseMigration
{
    /**
     * Run the migrations
     *
     * @return void
     */
    public function up()
    {
        $psw = \Str::random(12);
        $user = DB::connection('pgsql-kea')->getConfig('username');

        // Add directory structure
        if (! is_dir('/etc/kea/gateways6')) {
            mkdir('/etc/kea/gateways6', 0750, true);
        }

        // create wrong directory due to bug https://gitlab.isc.org/isc-projects/kea/-/issues/1144 - Remove on CentOS 8!
        if (! is_dir('/var/lib/lib/kea/')) {
            mkdir('/var/lib/lib/kea/', 0750, true);
        }

        system('chown -R apache /etc/kea/');
        // system("sed -i 's/tag VARCHAR(256) NOT NULL,/tag VARCHAR(191) NOT NULL,/' /usr/share/kea/scripts/mysql/dhcpdb_create.mysql");

        system("sed -i 's/^KEA_DB_PASSWORD=.*$/KEA_DB_PASSWORD=$psw/' /etc/nmsprime/env/provbase.env");
        Config::set('database.connections.pgsql-kea.password', $psw);
        DB::reconnect('pgsql-kea');

        system('sudo -u postgres /usr/pgsql-13/bin/psql -c "CREATE DATABASE kea"');
        system("sudo -u postgres /usr/pgsql-13/bin/psql -d kea -c \"CREATE USER $user PASSWORD '$psw';\"");
        system("/usr/sbin/kea-admin db-init pgsql -u $user -p $psw -n kea");

        system("sudo -u postgres /usr/pgsql-13/bin/psql -d kea -c \"
            GRANT ALL PRIVILEGES ON ALL Tables in schema public TO $user;
            GRANT ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA public TO $user;
        \"");

        echo "Change owner of kea DB tables to kea\n";

        system("for tbl in `sudo -u postgres /usr/pgsql-13/bin/psql -qAt -c \"select tablename from pg_tables where schemaname = 'public';\" kea`;
            do sudo -u postgres /usr/pgsql-13/bin/psql -d kea -c \"alter table ".'$tbl'.' owner to '.$user.'"; done');

        $find = [
            '<DB_USERNAME>',
            '<DB_PASSWORD>',
        ];

        $replace = [
            $user,
            $psw,
        ];

        $filename = '/etc/kea/dhcp6-nmsprime.conf';
        $content = file_get_contents($filename);
        preg_replace($find, $replace, $content);
        file_put_contents($filename, $content);

        system('systemctl enable kea-dhcp6');
    }

    /**
     * Reverse the migrations
     *
     * @return void
     */
    public function down()
    {
    }
}
