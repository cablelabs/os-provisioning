<?php

use Illuminate\Database\Migrations\Migration;

class BaseMigration extends Migration
{
    protected $caller_classname = null;
    protected $caller_migration_file = null;
    protected $called_up_table_generic = false;

    public function __construct()
    {
        $this->caller_classname = get_class($this);
        echo 'Migrating '.$this->caller_classname."\n";

        // get the filename of the caller class
        $reflector = new ReflectionClass($this->caller_classname);
        $this->caller_migration_file = basename($reflector->getFileName());

        if ($this->caller_migration_file < '2018_08_07') {
            // get and instanciate of index maker
            require_once getcwd().'/app/extensions/database/FulltextIndexMaker.php';
            $this->fim = new FulltextIndexMaker($this->tablename);
        } else {
            $this->fim = null;	// no indexes build on newer migrations (using InnoDB)
        }
    }

    public function up_table_generic(&$table)
    {
        $this->called_up_table_generic = true;

        // choose database engine and other stuff depending on date of migration
        // older migrations e.g. used MyISAM to build fulltext indexes
        if ($this->caller_migration_file < '2018_08_07') {
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
            throw new ErrorException('No FulltextIndexMaker in '.$this->caller_classname.'! – Maybe you want to index an InnoDB table?');
        }

        foreach ($fields as $field) {
            $this->fim->add($field);
        }

        // create FULLTEXT index including the given
        $this->fim->make_index();
    }

    public function __destruct()
    {
        if ((! $this->called_up_table_generic)
            &&
            (substr($this->caller_classname, 0, 6) == 'Create')
        ) {
            // only warn if not rolling back – unfortunately we have to step through the stacktrace…
            $rollback = false;
            foreach (debug_backtrace() as $caller) {
                if (\Str::contains(array_get($caller, 'class', ''), 'RollbackCommand')) {
                    $rollback = true;
                    break;
                }
            }
            if (! $rollback) {
                echo "\tWARNING: up_table_generic() not called from ".$this->caller_classname.'. Falling back to database defaults.';
            }
        }
    }
}
