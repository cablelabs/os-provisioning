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
class SwitchMysqltoPgsql extends BaseMigration
{
    public $migrationScope = 'system';
    public $databases = [];

    /**
     * Run the migrations.
     *
     * TODO: Remove Migration on next release - fresh installations will already be rolled out with Pgsql
     *
     * @return void
     */
    public function up()
    {
        // Don't run migration on fresh installation (mysql nmsprime DB does not exist)
        $mysqlRootConf = DB::connection('mysql-root')->getConfig();
        $ret = system('mysql -u '.$mysqlRootConf['username'].' -p'.$mysqlRootConf['password'].' --exec="SHOW DATABASES LIKE \'nmsprime\'"');

        if (! $ret) {
            // Fresh installation - mysql nmsprime DB doesn't exist
            return;
        }

        // Check if postgres and pgloader is installed before starting any action
        exec('systemctl status postgresql-13.service', $out, $ret);
        if ($ret) {
            throw new Exception('Postgresql-13 is missing.');
        }

        if (! system('which pgloader')) {
            throw new Exception('Pgloader is not installed. Install via: yum install pgloader');
        }

        $this->convertNmsprimeDbs();
        $this->fixNmsprimeDb();
        $this->switchKeaDb();
        $this->adaptRadius();
        $this->changeConfig();

        // Icinga is done via icinga-module-director RPM - see SPEC file

        DB::connection('mysql-root')->statement('DROP USER psqlconverter');
    }

    public function getDbsConf($db = '')
    {
        if (! $this->databases) {
            $this->databases = [
                'nmsprime' => [
                    'user' => DB::getConfig('username'),
                    'password' => DB::getConfig('password'),
                    'schema' => DB::getConfig('schema'),
                ],
                'nmsprime_ccc' => [
                    'user' => DB::connection('pgsql-ccc')->getConfig('username'),
                    'password' => DB::connection('pgsql-ccc')->getConfig('password'),
                    'schema' => DB::connection('pgsql-ccc')->getConfig('schema'),
                ],
            ];
        }

        if (! $db) {
            return $this->databases;
        }

        return $this->databases[$db];
    }

    /**
     * Convert MySQL NMSPrime DBs to PostgreSQL - add users and permissions
     */
    private function convertNmsprimeDbs()
    {
        foreach (['nmsprime', 'nmsprime_ccc'] as $db) {
            $conf = $this->getDbsConf($db);
            $user = $conf['user'];
            $schema = $conf['schema'];

            if ($db == 'nmsprime') {
                // DB already exists - Just add extension for indexing and quick search with % at start & end
                // Schema::createExtension('pg_trgm'); - only with https://github.com/tpetry/laravel-postgresql-enhanced
                system("sudo -u postgres /usr/pgsql-13/bin/psql -d $db -c 'CREATE EXTENSION IF NOT EXISTS pg_trgm'");
            } else {
                system("sudo -u postgres /usr/pgsql-13/bin/psql -c 'CREATE DATABASE $db'");
                echo "$db\n";

                // Convert MySQL DB to PostgreSQL
                exec("sudo -u postgres pgloader mysql://psqlconverter@localhost/$db postgresql:///$db", $ret);

                echo implode(PHP_EOL, $ret)."\n";
                $ret = [];

                // Create user
                system("sudo -u postgres /usr/pgsql-13/bin/psql -c \"CREATE USER $user PASSWORD '".$conf['password'].'\'"');
                echo "$user\n";

                // Move nmsprime_ccc table to schema public
                system("sudo -u postgres /usr/pgsql-13/bin/psql nmsprime_ccc -c 'ALTER TABLE nmsprime_ccc.cccauthuser SET SCHEMA public'");
            }

            // Set search path of postgres user to mainly used schema to not be required to always specify schema in queries
            system("sudo -u postgres /usr/pgsql-13/bin/psql $db -c \"ALTER ROLE postgres in DATABASE $db set search_path to '$schema'\"");
            // Move tables to public schema
            // $tables = [];
            // exec("sudo -u postgres /usr/pgsql-13/bin/psql $db -t -c \"SELECT table_name FROM information_schema.tables WHERE table_schema = 'nmsprime'\"", $tables);
            // system("sudo -u postgres /usr/pgsql-13/bin/psql $db -c 'ALTER TABLE $db.$table SET SCHEMA public'");

            // Grant permissions
            system("sudo -u postgres /usr/pgsql-13/bin/psql -d $db -c '
                GRANT USAGE ON SCHEMA $schema TO $user;
                GRANT ALL PRIVILEGES ON ALL Tables in schema $schema TO $user;
                GRANT ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA $schema TO $user;
                '");

            system("for tbl in `sudo -u postgres /usr/pgsql-13/bin/psql -qAt -c \"select tablename from pg_tables where schemaname = '$schema';\" $db`;
                do sudo -u postgres /usr/pgsql-13/bin/psql $db -c \"alter table $schema.".'$tbl'." owner to $user\"; done");

            system("sudo -u postgres /usr/pgsql-13/bin/psql nmsprime -c 'DROP function nmsprime.on_update_current_timestamp_authreminders CASCADE; DROP function nmsprime.on_update_current_timestamp_radpostauth CASCADE;'");
        }
    }

    /**
     * NMSPrime DB specific adaptions
     */
    private function fixNmsprimeDb()
    {
        if (Schema::hasTable('oid') && Module::collections()->has('HfcSnmp')) {
            \Modules\HfcSnmp\Entities\OID::where('type', 'u')->update(['type' => null]);
        }

        // Timestamps are with timezone now - hasMany with Timestamps results in errors in pivot tables
        if (Schema::hasTable('enviaorder_phonenumber')) {
            DB::statement('ALTER TABLE enviaorder_phonenumber
                ALTER COLUMN created_at TYPE TIMESTAMP without time zone,
                ALTER COLUMN updated_at TYPE TIMESTAMP without time zone,
                ALTER COLUMN deleted_at TYPE TIMESTAMP without time zone
            ');
        }

        DB::table('ippool')->where('netmask', '')->orWhere('ip_pool_start', '')->orWhere('ip_pool_end', '')->orWhere('router_ip', '')->delete();

        // IPs are stored as inet type and compared not with INET_ATON anymore
        // Generally we should use type cidr Using net::cidr for first column, but this can result in errors on insert and it's harder to validate - Possible validation could be: https://www.phpclasses.org/browse/file/70429.html
        if (Schema::hasTable('ippool')) {
            DB::statement('ALTER table ippool
                ALTER COLUMN net type inet USING net::inet,
                ALTER COLUMN ip_pool_start type inet USING ip_pool_start::inet,
                ALTER COLUMN ip_pool_end type inet USING ip_pool_end::inet,
                ALTER COLUMN router_ip type inet USING router_ip::inet
            ');

            DB::statement('ALTER table modem RENAME COLUMN ipv4 to ipv4_tmp');
            DB::statement('ALTER table modem add column ipv4 inet');
            DB::raw('UPDATE modem set ipv4 = \'0.0.0.0\'::inet + ipv4_tmp');
            DB::statement('ALTER table modem drop column ipv4_tmp;');
            // ALTER COLUMN broadcast_ip type inet USING broadcast_ip::inet,

            foreach (\Modules\ProvBase\Entities\IpPool::withTrashed()->get() as $ippool) {
                \Modules\ProvBase\Entities\IpPool::where('id', $ippool->id)->update(['net' => $ippool->net.$ippool->maskToCidr()]);
            }

            DB::statement('ALTER table ippool drop column netmask;');

            // Change wrong not nullable fields (already in Mysql)
            DB::statement('ALTER table contract
                ALTER COLUMN internet_access drop not null,
                ALTER COLUMN create_invoice drop not null,
                ALTER COLUMN has_telephony drop not null
            ');
            DB::statement('ALTER table modem ALTER COLUMN next_passive_id drop not null');
        }

        if (Schema::hasTable('settlementrun')) {
            DB::statement('ALTER table settlementrun
                ALTER COLUMN verified drop not null,
                ALTER COLUMN fullrun drop not null;
            ');
        }

        if (Schema::hasTable('cccauthuser')) {
            DB::connection('pgsql-ccc')->statement('ALTER table cccauthuser ALTER COLUMN description drop not null');
        }

        if (Schema::hasTable('netelement')) {
            // Simulate virtual column of netelement
            DB::statement('ALTER TABLE netelement drop column id_name');
            DB::statement("ALTER TABLE netelement add column id_name varchar generated always as (CASE WHEN name IS NULL THEN cast(id as varchar) WHEN id is NULL THEN name ELSE name || '_' || cast(id as varchar) END) stored");
        }

        if (Schema::hasTable('notifications')) {
            DB::statement('ALTER TABLE notifications alter COLUMN read_at type timestamp without time zone;');
            DB::statement('ALTER TABLE notifications alter COLUMN created_at type timestamp without time zone;');
            DB::statement('ALTER TABLE notifications alter COLUMN updated_at type timestamp without time zone;');
        }

        if (Schema::hasTable('mpr')) {
            DB::statement('ALTER TABLE mpr drop COLUMN prio;');
            DB::statement('ALTER TABLE mpr drop COLUMN type;');
            DB::statement('ALTER TABLE mprgeopos drop COLUMN name;');
            DB::statement('ALTER TABLE mprgeopos drop COLUMN description;');
        }

        // Drop RADIUS tables as they will be in own database
        $radTables = ['nas', 'radacct', 'radcheck', 'radgroupcheck', 'radgroupreply', 'radippool', 'radpostauth', 'radreply', 'radusergroup'];

        foreach ($radTables as $table) {
            DB::statement("DROP table $table");
        }
    }

    /**
     * Use Postgres for Kea DHCP - give nmsprime user all permissions
     */
    private function switchKeaDb()
    {
        $user = 'kea';
        $psw = \Str::random(12);
        $envPath = '/etc/nmsprime/env/provbase.env';

        exec("grep 'KEA_DB_PASSWORD' $envPath", $exists);

        if ($exists) {
            system("sed -i 's/^KEA_DB_PASSWORD=.*$/KEA_DB_PASSWORD=$psw/' /etc/nmsprime/env/provbase.env");
        } else {
            file_put_contents($envPath, "# Configuration for database used by kea\nKEA_DB_HOST=localhost\n
KEA_DB_DATABASE=kea\nKEA_DB_USERNAME=kea\nKEA_DB_PASSWORD=$psw", FILE_APPEND);
        }

        Config::set('database.connections.pgsql-kea.password', $psw);
        DB::reconnect('pgsql-kea');

        system('sudo -u postgres /usr/pgsql-13/bin/psql -c "CREATE DATABASE kea"');
        echo "kea\n";
        system("sudo -u postgres /usr/pgsql-13/bin/psql -d kea -c \"CREATE USER $user PASSWORD '$psw';\"");

        // (1) Initialise new kea DB for Pgsql (contains less tables than mysql schema)
        system("/usr/sbin/kea-admin db-init pgsql -u $user -p $psw -n kea");

        // Add permissions
        system("sudo -u postgres /usr/pgsql-13/bin/psql kea -c \"
            GRANT ALL PRIVILEGES ON ALL Tables in schema public TO $user;
            GRANT ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA public TO $user;
        \"");

        system('sudo -u postgres /usr/pgsql-13/bin/psql kea -c "ALTER ROLE postgres set search_path to \'public\'"');

        echo "Change owner of kea DB tables to kea\n";

        system("for tbl in `sudo -u postgres /usr/pgsql-13/bin/psql kea -qAt -c \"select tablename from pg_tables where schemaname = 'public';\"`;
            do sudo -u postgres /usr/pgsql-13/bin/psql kea -c \"alter table ".'$tbl'.' owner to '.$user.'"; done');

        // Transfer leases
        foreach (DB::connection('mysql')->table('kea.lease6')->get() as $lease) {
            DB::connection('pgsql-kea')->table('lease6')->insert((array) $lease);
        }

        system('sed -i \'s/"type": "mysql"/"type": "postgresql"/\' /etc/kea/dhcp6-nmsprime.conf');
        system('systemctl restart kea-dhcp6');
    }

    /**
     * Adapt RADIUS Server to use postgresql and use separate database
     */
    private function adaptRadius()
    {
        $db = $user = 'radius';
        $psw = \Str::random(12);

        system('sed -i "s/^#RADIUS_DB/RADIUS_DB/" /etc/nmsprime/env/provbase.env');
        system("sed -i 's/^RADIUS_DB_PASSWORD=.*$/RADIUS_DB_PASSWORD=$psw/' /etc/nmsprime/env/provbase.env");

        system("sudo -u postgres /usr/pgsql-13/bin/psql -c 'CREATE DATABASE $db'");
        echo "radius\n";

        DB::connection('pgsql-radius')->unprepared(file_get_contents('/etc/raddb/mods-config/sql/main/postgresql/schema.sql'));
        DB::connection('pgsql-radius')->unprepared(file_get_contents('/etc/raddb/mods-config/sql/ippool/postgresql/schema.sql'));
        \Artisan::call('nms:raddb-repopulate');

        // Add radius user
        system("sudo -u postgres /usr/pgsql-13/bin/psql -d radius -c \"
            CREATE USER $user PASSWORD '$psw';
            GRANT USAGE ON SCHEMA public TO $user;
            GRANT ALL PRIVILEGES ON ALL Tables in schema public TO $user;
            GRANT ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA public TO $user;
            \"");

        system('sudo -u postgres /usr/pgsql-13/bin/psql radius -c "ALTER ROLE postgres set search_path to \'public\'"');

        system("for tbl in `sudo -u postgres /usr/pgsql-13/bin/psql -qAt -c \"select tablename from pg_tables where schemaname = 'public';\" radius`;
            do sudo -u postgres /usr/pgsql-13/bin/psql -d radius -c \"alter table ".'$tbl'." owner to $user\"; done");

        // Adapt /etc/raddb/ config
        system("sed -e 's|dialect = \"mysql\"|dialect = \"postgresql\"|' \\
            -i /etc/raddb/mods-available/sql /etc/raddb/mods-available/sqlippool");
        system("sed -e 's|driver = \"rlm_sql_mysql\"|driver = \"rlm_sql_postgresql\"|' \\
            -e 's|login = \"nmsprime\"|login = \"$user\"|' \\
            -e 's|password = \".*\"|password = \"$psw\"|' \\
            -e 's|radius_db = \".*\"|radius_db = \"radius\"|' \\
            -e 's|#server = \".*\"|server = \"localhost\"|' \\
            -i /etc/raddb/mods-available/sql");
    }

    private function changeConfig()
    {
        system("sed -i 's/QUEUE_DRIVER_DATABASE_CONNECTION=mysql/QUEUE_DRIVER_DATABASE_CONNECTION=pgsql/' /etc/nmsprime/env/global.env");
        system("sed -i 's/ROOT_DB_DATABASE=nmsprime/ROOT_DB_DATABASE=cacti/' /etc/nmsprime/env/root.env");

        \Artisan::call('config:cache');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        system('sed -i \'s/"type": "postgresql"/"type": "mysql"/\' /etc/kea/dhcp6-nmsprime.conf');
        system('systemctl restart kea-dhcp6');

        system("sed -i 's/dialect = \"postgresql\"/dialect = \"mysql\"/' /etc/raddb/mods-available/sql");
        system('systemctl restart radiusd');

        // Remove kea and radius DB and user
        system("sudo -u postgres /usr/pgsql-13/bin/psql -c 'drop database kea;'");
        system("sudo -u postgres /usr/pgsql-13/bin/psql -c 'drop owned by kea; drop user kea;'");
        system("sudo -u postgres /usr/pgsql-13/bin/psql -c 'drop database radius;'");
        system("sudo -u postgres /usr/pgsql-13/bin/psql -c 'drop owned by radius; drop user radius;'");

        // Remove nmsprime and icinga DBs and users
        $dbs = $this->getDbsConf();

        foreach ($dbs as $db => $config) {
            if ($db == 'nmsprime') {
                system("sudo -u postgres /usr/pgsql-13/bin/psql -d $db -c 'drop schema $db cascade'");
            } else {
                system("sudo -u postgres /usr/pgsql-13/bin/psql -c 'drop database $db'");
            }

            $user = $config['user'];

            system("sudo -u postgres /usr/pgsql-13/bin/psql -c 'DROP OWNED BY $user; drop user $user'");
        }

        // Config::set('database.connections.default', 'mysql');
    }
}
