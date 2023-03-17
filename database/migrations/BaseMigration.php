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

namespace Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;

class BaseMigration extends Migration
{
    protected $callerClassname = '';
    protected $callerMigrationFile = '';
    protected $calledUpTableGeneric = false;

    /**
     * Define the migration's scope:
     *   - “database”: Migration changes database
     *   - “system”: Changes in configfiles, calling of systemd commands, …
     * Beginning in 2021 a migration is not allowed ot change database AND system stuff
     * Write two migrations if needed!
     */
    public $migrationScope = '';

    public function __construct()
    {
        $this->callerClassname = get_class($this);
        echo 'Migrating '.$this->callerClassname."\n";

        // get the filename of the caller class
        $reflector = new \ReflectionClass($this->callerClassname);
        $this->callerMigrationFile = basename($reflector->getFileName());

        // check if scope is set in newer migrations
        if ($this->callerMigrationFile >= '2021_') {
            if (! in_array($this->migrationScope, ['database', 'system'])) {
                // this is meant as a hint for developing and should never be reached in production
                exit("\nERROR in $this->callerMigrationFile: ".$this->callerClassname."->migrationScope has to be “database” or “system”. Exiting…\n\n");
            }
        }

        // check if migration shall be executed
        if (! $this->migrationShallRun()) {
            exit(0);
        }

        if ($this->callerMigrationFile < '2018_08_07') {
            // get and instanciate of index maker
            require_once base_path().'/app/extensions/database/FulltextIndexMaker.php';
            $this->fim = new FulltextIndexMaker($this->tablename);
        } else {
            $this->fim = null;	// no indexes build on newer migrations (using InnoDB)
        }

        \DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
        Schema::defaultStringLength(191);
    }

    /**
     * Check if the migration shall be executed.
     *
     * @return bool
     *
     * @author Patrick Reichel
     */
    public function migrationShallRun()
    {
        if (\Module::collections()->has('ProvHA')) {
            // check if called from slave migration command – then force execution
            $filetrace = [];
            foreach (debug_backtrace() as $trace) {
                $filetrace[] = $trace['file'] ?? 'nofile';
            }

            // run migration if called from wrapper command
            if (in_array('/var/www/nmsprime/modules/ProvHA/Console/MigrateSlaveCommand.php', $filetrace)) {
                return true;
            }

            // do not execute migrations directly on HA slave machines
            if (config('provha.hostinfo.ownState') == 'slave') {
                echo '! Ignoring migration '.$this->callerClassname." – this is a slave machine.\n";

                return false;
            }
        }

        // default – run the migration
        return true;
    }

    public function up_table_generic(&$table)
    {
        $this->calledUpTableGeneric = true;

        $table->bigIncrements('id');
        // older migrations e.g. used MyISAM to build fulltext indexes
        // see 4.2: https://wiki.postgresql.org/wiki/Don%27t_Do_This#Don.27t_use_timestamp_.28without_time_zone.29_to_store_UTC_times
        $table->timestampsTz(null);
        $table->softDeletesTz('deleted_at', null);
    }

    public function up()
    {
    }

    protected function set_auto_increment($i)
    {
        if (! $this->tablename) {
            return;
        }

        \DB::update('ALTER TABLE '.$this->tablename." AUTO_INCREMENT = $i;");
    }

    /**
     * All columnes to be fulltext indexed.
     * Attention on updating an existing index: The index will be dropped and re-created – therefore you have to give ALL columns (again)!!
     */
    protected function set_fim_fields($fields)
    {
        if (is_null($this->fim)) {
            throw new \ErrorException('No FulltextIndexMaker in '.$this->callerClassname.'! – Maybe you want to index an InnoDB table?');
        }

        foreach ($fields as $field) {
            $this->fim->add($field);
        }

        // create FULLTEXT index including the given
        $this->fim->make_index();
    }

    public function addIndex(string $column)
    {
        Schema::table($this->tableName, function (Blueprint $table) use ($column) {
            $indices = Schema::getConnection()
                ->getDoctrineSchemaManager()
                ->listTableIndexes($this->tableName);

            if (! array_key_exists("{$this->tableName}_{$column}_index", $indices)) {
                $table->index($column);
            }
        });
    }

    public function __destruct()
    {
        if ((! $this->calledUpTableGeneric) &&
            (substr($this->callerClassname, 0, 6) == 'Create')
        ) {
            // only warn if not rolling back – unfortunately we have to step through the stacktrace…
            $rollback = false;
            foreach (debug_backtrace() as $caller) {
                if (\Str::contains(Arr::get($caller, 'class', ''), 'RollbackCommand')) {
                    $rollback = true;
                    break;
                }
            }
            if (! $rollback) {
                echo "\tWARNING: up_table_generic() not called from {$this->callerClassname}. Falling back to database defaults.\n";
            }
        }
    }
}
