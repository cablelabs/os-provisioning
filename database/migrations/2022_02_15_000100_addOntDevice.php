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

class AddOntDevice extends BaseMigration
{
    public $migrationScope = 'database';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /* DB::statement("ALTER TABLE configfile MODIFY COLUMN device ENUM('cm', 'mta', 'tr069', 'ont') NOT NULL"); // MariaDB version*/
        system('sudo -u postgres /usr/pgsql-13/bin/psql -d nmsprime -c "ALTER TYPE nmsprime.configfile_device ADD VALUE \'ont\'"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // reverse statement does not exist in Postgres(?)
        /* DB::statement("ALTER TYPE nmsprime.configfile_device AS ENUM ('cm', 'mta', 'tr069')"); */
    }
}
