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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\Console\Output\ConsoleOutput;

class BaseMigration extends Migration
{
    use \Illuminate\Console\Concerns\InteractsWithIO;

    protected $callerClassname = '';
    protected $callerMigrationFile = '';
    protected $calledUpTableGeneric = false;

    /**
     * Defines the migration's scope. Beginning in 2021 a migration is not
     * allowed ot change database AND system stuff. Write two migrations
     * if needed!
     *
     * @var string Available options:
     *             “database”: Migration changes database
     *             “system”: Changing configfiles, calling of systemd commands…
     */
    public $migrationScope;

    /**
     * Set the table name to migrate. This has proven to reduce copy and paste
     * errors as the table name is written in only one place-
     *
     * @var string
     */
    protected $tableName;

    /**
     * The output interface implementation.
     *
     * @var \Illuminate\Console\OutputStyle
     */
    protected $output;

    public function __construct()
    {
        $this->output = new ConsoleOutput();

        $this->callerClassname = get_class($this);
        $reflector = new \ReflectionClass($this->callerClassname);
        $this->callerMigrationFile = basename($reflector->getFileName());
        $this->info("Migrating {$this->callerClassname}.", 'v');

        $this->checkMigrationScope();
        $this->checkForHighAvailabilitySetup();

        DB::getDoctrineSchemaManager()
            ->getDatabasePlatform()
            ->registerDoctrineTypeMapping('enum', 'string');

        Schema::defaultStringLength(191);
    }

    public function up_table_generic(&$table)
    {
        $table->bigIncrements('id');
        $table->timestampsTz(null);
        $table->softDeletesTz('deleted_at', null);

        $this->calledUpTableGeneric = true;
    }

    /**
     * Helper method to quickly add an index to a table
     *
     * @param  string  $column  name of the column
     * @return void
     */
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

    /**
     * This is meant as a hint for developing and should never be reached in
     * production.
     *
     * @return void
     */
    private function checkMigrationScope(): void
    {
        if ($this->callerMigrationFile >= '2021_' && ! in_array($this->migrationScope, ['database', 'system'])) {
            $this->error(
                "ERROR in {$this->callerMigrationFile}: {$this->callerClassname}->migrationScope ".
                'has to be "database" or "system". Exiting…'
            );
            exit(1);
        }
    }

    /**
     * Check if the migration shall be executed.
     *
     * @return bool
     *
     * @author Patrick Reichel
     */
    private function checkForHighAvailabilitySetup(): void
    {
        if (! \Module::collections()->has('ProvHA')) {
            return;
        }

        // check if called from slave migration command – then force execution
        $filetrace = [];
        foreach (debug_backtrace() as $trace) {
            $filetrace[] = $trace['file'] ?? 'nofile';
        }

        // run migration if called from wrapper command
        if (in_array('/var/www/nmsprime/modules/ProvHA/Console/MigrateSlaveCommand.php', $filetrace)) {
            return;
        }

        // do not execute migrations directly on HA slave machines
        if (config('provha.hostinfo.ownState') == 'slave') {
            $this->warn("! Ignoring migration {$this->callerClassname} – this is a slave machine.");

            exit(0);
        }
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
                $this->warn("WARNING: up_table_generic() not called from {$this->callerClassname}. Falling back to database defaults.");
            }
        }
    }
}
