<?php

use Illuminate\Database\Migrations\Migration;

/**
 * We first planned to use FULLTEXT indexes on our tables – so most of them are created using MyISAM.
 * This policy changed – we convert all tables to InnoDB.
 *
 * Attention:
 * If one enables a module – say ProvVoip – after running this migration all tables created there are MyISAM.
 * To fix this there is an derived migration class for each module – keep that in mind if changing here!
 *
 * @author Patrick Reichel
 */
class ChangeEngineToInnodb extends Migration
{
    protected $migration_class = null;
    protected $migration_file = null;
    protected $logpath = 'migrations';	// relative to storage path
    protected $logfile = null;

    // add the names of tables that should not be converted to InnoDB
    protected $tables_to_ignore = [];

    /**
     * Constructor.
     * Used to set some environmental variables.
     *
     * @author Patrick Reichel
     */
    public function __construct()
    {

        // get the caller class
        $this->migration_class = get_class($this);

        echo 'Migrating '.$this->migration_class."\n";

        // get the filename of the caller class
        $reflector = new ReflectionClass($this->migration_class);
        $this->migration_file = $reflector->getFileName();

        // set filename
        $this->logfile = $this->logpath.'/'.basename($this->migration_file).'.json';

        // Illuminate\Database\Migrations\Migration is abtract class; no parent constructor to be called :-)
    }

    /**
     * The migrations.
     * First we have to drop all FULLTEXT indexes, then we convert tables to InnoDB.
     *
     * Additionally we log all conversions in storage – this can later be used in $this->down() to revert it.
     *
     * @author Patrick Reichel
     */
    public function up()
    {
        $processed = [];
        $processed['meta'] = [];	// for meta information
        $processed['meta']['migration_class'] = $this->migration_class;
        $processed['meta']['migration_file'] = $this->migration_file;
        $processed['meta']['migration_start'] = date('c');
        $processed['migrations'] = [];
        $processed['errors'] = [];

        \Storage::makeDirectory($this->logpath);
        $tables = DB::select('SHOW TABLE STATUS');

        foreach ($tables as $table) {
            $tablename = $table->Name;
            $engine = $table->Engine;

            // check if already InnoDB – nothing to do; already using the engine we want
            if ($engine == 'InnoDB') {
                continue;
            }

            // check if current table shall be converted
            if (in_array($tablename, $this->tables_to_ignore)) {
                echo "\t$tablename excluded from conversion to InnoDB – skipping…\n";
                continue;
            }

            // check if there are FULLTEXT indexes and delete them
            $indexes_raw = DB::select('SHOW INDEX FROM '.$tablename);
            $indexes = [];
            foreach ($indexes_raw as $index_raw) {
                if ($index_raw->Index_type == 'FULLTEXT') {
                    $indexes[$index_raw->Key_name] = 'FULLTEXT';
                }
            }

            foreach ($indexes as $index => $type) {
                $sql = "DROP INDEX $index ON $tablename";
                echo "\t$sql: ";
                if (DB::statement($sql)) {
                    echo "Success\n";
                } else {
                    echo "ERROR\n";
                }
            }

            // try to convert table to InnoDB
            $sql = "ALTER TABLE $tablename ENGINE=InnoDB";
            echo "\t$sql: ";
            if (DB::statement($sql)) {
                echo "Success\n";
                $processed['migrations'][$tablename] = [
                    'from' => $engine,
                    'to' => 'InnoDB',
                    'date' => date('c'),
                ];
            } else {
                $processed['errors'][$tablename] = [
                    'date' => date('c'),
                ];
                echo "ERROR\n";
            }

            // log every single migration – make sure we have complete data even if this migration crashes on the next loop cycle
            \Storage::put($this->logfile, json_encode($processed, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT));
        }
        // finally: write file (again) – even if there are no conversions
        \Storage::put($this->logfile, json_encode($processed, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT));
    }

    /**
     * Rollback the conversions to previous database engine.
     * We don't rebuild the indexes.
     *
     * @author Patrick Reichel
     */
    public function down()
    {

        // build helpers to to verify the input (prevent SQL injections)
        // reason: the json file may be corrupted
        $supported_engines = [
            'MyISAM',
        ];
        $tables_raw = DB::select('SHOW TABLE STATUS');
        $tables = [];
        foreach ($tables_raw as $table) {
            array_push($tables, $table->Name);
        }

        try {
            $migration_data = json_decode(\Storage::get($this->logfile), true);
        } catch (Exception $ex) {
            echo "\tError reading ".$this->logfile.': '.get_class($ex).' ('.$ex->getMessage().")\n";
            $migration_data['migrations'] = [];	// we can do nothing
        }

        foreach ($migration_data['migrations'] as $tablename => $data) {
            if (! in_array($data['from'], $supported_engines)) {
                echo "\tERROR: ".$data['from']." on table $tablename is not a supported database engine. Cannot convert!\n";
                continue;
            }
            if (! in_array($tablename, $tables)) {
                echo "\tERROR: Table $tablename does not exist. Cannot convert!\n";
                continue;
            }

            // the variables have been checked – now it is save to build the query directly
            $sql = "ALTER TABLE $tablename ENGINE=".$data['from'];
            echo "\t$sql: ";
            if (DB::statement($sql)) {
                echo "Success\n";
            } else {
                echo "ERROR\n";
            }
        }

        // finally: remove the file holding informations about conversions
        \Storage::delete($this->logfile);
    }
}
