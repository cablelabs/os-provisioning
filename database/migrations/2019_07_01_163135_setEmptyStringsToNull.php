<?php

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SetEmptyStringsToNull extends Migration
{
    public function __construct()
    {
        DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
        Schema::defaultStringLength(191);
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // get all models and all table names
        $models = collect(\App\BaseModel::get_models())->mapWithKeys(function ($namespace, $modelName) {
            return [strtolower($modelName) => $namespace];
        });
        $tables = collect(Schema::getConnection()->getDoctrineSchemaManager()->listTableNames());

        // ignore tables of Laravel, Bouncer Package, always filled models and "n to m"-Relationships
        $except = [
            'abilities',                // Bouncer
            'accountingrecord',         // not set by user
            'assigned_roles',           // Bouncer
            'authreminders',            // legacy table ?
            'carriercode',              // not set by user
            'ekpcode',                  // not set by user
            'enviaorder_phonenumber',   // n to m
            'failed_jobs',              // Laravel
            'guilog',                   // not set by user
            'item',                     // n to m
            'jobs',                     // Laravel
            'migrations',               // Laravel
            'permissions',              // Bouncer
            'roles',                    // Bouncer
            'trcclass',                 // not set by user
            'ticket_user',              // n to m
            'tickettype_ticket',        // n to m
            'users',                    // already configured
        ];

        $tables = $tables->flip()->forget($except)->keys();

        foreach ($tables as $tableName) {
            // call rules for model of table
            $modelName = strtolower(studly_case($tableName));
            $rules = $models->has($modelName) ? $models[$modelName]::rules() : [];

            // get rules for this table
            $nullable = [];
            foreach ($rules as $field => $rule) {
                $nullable[$field] = (in_array('required', explode('|', $rule))) ? false : true;
            }

            // get all column names
            foreach (DB::getSchemaBuilder()->getColumnListing($tableName) as $column) {
                $rawType = $this->getTableColumns($tableName)[$column];

                // if type != enum
                if (Str::startsWith($rawType, 'enum')) {
                    continue;
                }

                $type = DB::connection()->getDoctrineColumn($tableName, $column)->getType()->getName();

                if ($column == 'id') {
                    continue;
                }

                if ($type == 'smallint') {
                    $type = 'smallInteger';
                }

                Schema::table($tableName, function (Blueprint $table) use ($column, $nullable, $type) {
                    if (array_key_exists($column, $nullable)) {
                        return $table->$type($column)->nullable($nullable[$column])->change();
                    }

                    $table->$type($column)->nullable()->change();
                });

                // set empty strings to NULL
                DB::statement("UPDATE $tableName SET `$column`=NULL WHERE `$column`='';");
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }

    public function getTableColumns($table)
    {
        $table_info_columns = DB::select(DB::raw("DESCRIBE $table;"));
        $test = collect([]);

        foreach ($table_info_columns as $column) {
            $test->put($column->Field, $column->Type);
        }

        return $test->toArray();
    }
}
