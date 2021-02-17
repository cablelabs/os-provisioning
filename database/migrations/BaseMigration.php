<?php

use Illuminate\Support\Arr;
use Illuminate\Database\Migrations\Migration;

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
        $reflector = new ReflectionClass($this->callerClassname);
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
            require_once getcwd().'/app/extensions/database/FulltextIndexMaker.php';
            $this->fim = new FulltextIndexMaker($this->tablename);
        } else {
            $this->fim = null;	// no indexes build on newer migrations (using InnoDB)
        }

        DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
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
            if (config('provha.hostinfo.own_state') == 'slave') {
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

        // choose database engine and other stuff depending on date of migration
        // older migrations e.g. used MyISAM to build fulltext indexes
        if ($this->callerMigrationFile < '2018_08_07') {
            $table->increments('id');
            $table->engine = 'MyISAM'; // InnoDB doesn't support fulltext index in MariaDB < 10.0.5
            $table->timestamps();
            $table->softDeletes();
        } else {
            $table->increments('id');
            $table->engine = 'InnoDB';
            $table->timestamps();
            $table->softDeletes();
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
        }
    }

    public function up()
    {
    }

    protected function set_auto_increment($i)
    {
        DB::update('ALTER TABLE '.$this->tablename." AUTO_INCREMENT = $i;");
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

    public function __destruct()
    {
        if ((! $this->calledUpTableGeneric) &&
            (substr($this->callerClassname, 0, 6) == 'Create')
        ) {
            // only warn if not rolling back – unfortunately we have to step through the stacktrace…
            $rollback = false;
            foreach (debug_backtrace() as $caller) {
                if (Str::contains(Arr::get($caller, 'class', ''), 'RollbackCommand')) {
                    $rollback = true;
                    break;
                }
            }
            if (! $rollback) {
                echo "\tWARNING: up_table_generic() not called from ".$this->callerClassname.'. Falling back to database defaults.';
            }
        }
    }
}
