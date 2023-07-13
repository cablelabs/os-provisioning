<?php

use Database\Migrations\BaseMigration;
use Modules\ProvBase\Entities\RadAcct;
use Modules\ProvBase\Entities\RadIpPool;
use Modules\ProvBase\Entities\RadPostAuth;

class MigrateMysqlToPostgres extends BaseMigration
{
    public $migrationScope = 'database';

    protected $tableName = '';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->adaptRadius();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        system("sed -i 's/dialect = \"postgresql\"/dialect = \"mysql\"/' /etc/raddb/mods-available/sql");
        system('systemctl restart radiusd');
        system("sudo -u postgres /usr/pgsql-13/bin/psql -c 'drop database radius;'");
        system("sudo -u postgres /usr/pgsql-13/bin/psql -c 'drop owned by radius; drop user radius;'");
    }

    /**
     * Adapt RADIUS Server to use postgresql and use separate database
     */
    private function adaptRadius()
    {
        $db = $user = 'radius';
        $psw = DB::connection('pgsql-radius')->getConfig('password');

        system("sudo -u postgres /usr/pgsql-13/bin/psql -c 'CREATE DATABASE $db'");
        echo "radius\n";

        // Add radius user
        system("sudo -u postgres /usr/pgsql-13/bin/psql -d $db -c \"
            CREATE USER $user PASSWORD '$psw';
            GRANT USAGE ON SCHEMA public TO $user;
            GRANT ALL PRIVILEGES ON ALL Tables in schema public TO $user;
            GRANT ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA public TO $user;
            \"");

        DB::connection('pgsql-radius')->unprepared(file_get_contents('/etc/raddb/mods-config/sql/main/postgresql/schema.sql'));
        DB::connection('pgsql-radius')->unprepared(file_get_contents('/etc/raddb/mods-config/sql/ippool/postgresql/schema.sql'));

        \Artisan::call('nms:raddb-repopulate');

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
            -e 's|#\s*server = \".*\"|\tserver = \"localhost\"|' \\
            -i /etc/raddb/mods-available/sql");

        // Get entries of radippool, radacct, radpostauth
        $allocatedIps = DB::connection('mysql')->table('radippool')->whereNotNull('expiry_time')->where('expiry_time', '>', now())->get();
        foreach ($allocatedIps as $radip) {
            RadIpPool::where('framedipaddress', $radip->framedipaddress)->update([
                'callingstationid' => $radip->callingstationid,
                'expiry_time' => $radip->expiry_time,
                'username' => $radip->username,
            ]);
        }

        $radaccts = DB::connection('mysql')->table('radacct')->orderBy('radacctid', 'desc')->groupBy('framedipaddress')->get();
        foreach ($radaccts as $radacct) {
            $data = (array) $radacct;
            unset($data['radacctid']);

            $radacct = new RadAcct();
            foreach ($data as $key => $value) {
                $radacct->$key = $value;
            }

            $radacct->saveQuietly();
        }

        $radpostauths = DB::connection('mysql')->table('radpostauth')->orderBy('id', 'desc')->groupBy('username')->get();
        foreach ($radpostauths as $radpa) {
            $data = (array) $radpa;
            unset($data['id']);

            $radpa = new RadPostAuth();
            foreach ($data as $key => $value) {
                $radpa->$key = $value;
            }

            $radpa->saveQuietly();
        }
    }
}
