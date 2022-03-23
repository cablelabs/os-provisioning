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
class ConnectRadiusAndNmsprimeDb extends BaseMigration
{
    public $migrationScope = 'database';

    /**
     * Run the migrations.
     *
     * Make radius table radacct accessable from nmsprime DB for nmsprime user
     *
     * @return void
     */
    public function up()
    {
        $pass = DB::connection('pgsql-radius')->getConfig('password');

        system("sudo -u postgres /usr/pgsql-13/bin/psql nmsprime << EOF
            CREATE EXTENSION postgres_fdw;
            CREATE Server \"nmsprime-radius\" FOREIGN DATA WRAPPER postgres_fdw OPTIONS ( host '127.0.0.1', dbname 'radius', port '5432');
            CREATE user MAPPING FOR nmsprime server \"nmsprime-radius\" OPTIONS ( user 'radius', password '$pass');
            CREATE user MAPPING FOR postgres server \"nmsprime-radius\" OPTIONS ( user 'radius', password '$pass');
            IMPORT FOREIGN SCHEMA public limit to (radacct) from server \"nmsprime-radius\" into nmsprime;

            GRANT USAGE on FOREIGN server \"nmsprime-radius\" to nmsprime;
            ALTER foreign table nmsprime.radacct owner to nmsprime;
EOF");

        /* Verify
            select * from pg_user_mapping;
            select * from pg_foreign_server;
            select * from pg_foreign_table;
         */
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        system("sudo -u postgres /usr/pgsql-13/bin/psql nmsprime -c 'DROP SERVER \"nmsprime-radius\" cascade'");
    }
}
