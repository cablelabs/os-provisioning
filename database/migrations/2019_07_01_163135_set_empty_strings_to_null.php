<?php

use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SetEmptyStringsToNull extends Migration
{
    protected $path;

    protected const TABLES = [
        'global_config',
        'sla',
        'supportrequest',
        'users',
    ];

    public function __construct()
    {
        $this->path = dirname((new ReflectionClass(get_called_class()))->getFileName());
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
        $tables = collect(Schema::getConnection()->getDoctrineSchemaManager()->listTableNames())
            ->intersect($this->getModuletables());

        // tables of Laravel, Bouncer Package, programmatic filled models and "n to m"-Relationships
        $skipped = [
            'abilities',                // Bouncer
            'accountingrecord',         // not set by user
            'assigned_roles',           // Bouncer
            'authreminders',            // legacy table ?
            'carriercode',              // not set by user
            'csv_data',                 // only programmatic generated data
            'debt',                     // already correct configured
            'ekpcode',                  // not set by user
            'enviaorder_phonenumber',   // n to m
            'failed_jobs',              // Laravel
            'guilog',                   // not set by user
            'jobs',                     // Laravel
            'migrations',               // Laravel
            'overduedebts',             // already correct configured
            'permissions',              // Bouncer
            'realty',                   // already correct configured
            'roles',                    // Bouncer
            'trcclass',                 // not set by user
            'ticket_user',              // n to m
            'tickettype_ticket',        // n to m
            // freeradius tables
            'radacct',
            'radcheck',
            'radgroupcheck',
            'radgroupreply',
            'radippool',
            'radpostauth',
            'radreply',
            'radusergroup',
            'nas',
        ];

        // skip chosen tables
        $tables = $tables->flip()->forget($skipped)->keys();

        foreach ($tables as $tableName) {
            // call rules for model of table
            $modelName = $tableName == 'users' ? 'user' : strtolower(studly_case($tableName));
            $instance = new $models[$modelName];
            $rules = $models->has($modelName) ? $instance->rules() : [];

            // get rules for this table
            $nullable = [];
            $required = ['sometimes', 'required'];
            foreach ($rules as $field => $rule) {
                $nullable[$field] = empty(array_intersect($required, explode('|', $rule))) ? true : false;
            }

            echo "Set empty strings to null in {$tableName} table.\n";

            // get all column names
            $tableColumns = $this->getTableColumns($tableName);
            foreach (DB::getSchemaBuilder()->getColumnListing($tableName) as $column) {
                $rawType = $tableColumns[$column]['type'];

                // if type != enum
                if (Str::startsWith($rawType, 'enum')) {
                    continue;
                }

                $type = DB::connection()->getDoctrineColumn($tableName, $column)->getType()->getName();

                // exceptions: id, booleans and foreign keys
                if (
                    $column === 'id' ||
                    $type === 'boolean' ||
                    ($column !== 'parent_id' && $type == 'integer' && Str::endsWith($column, '_id'))
                ) {
                    continue;
                }

                // New Default String length for VarChar should be 191 for future indexing purposes
                $isDefaultStrLen = $type === 'string' && $tableColumns[$column]['length'][0] == 255;
                $length = in_array($type, ['integer', 'smallint', 'bigint']) || $isDefaultStrLen ? [] :
                        $tableColumns[$column]['length'];

                // Doctrine Type and Function name differ
                if ($type === 'smallint' || $type === 'bigint') {
                    $type = str_replace('int', '', $type).'Integer';
                }

                // keep unsigned property of column
                if (Str::contains($rawType, 'unsigned')) {
                    $type = 'unsigned'.ucfirst($type);
                }

                // Alter the Table Schema
                Schema::table(
                    $tableName,
                    function (Blueprint $table) use ($column, $length, $nullable, $type) {
                        // nullable set according to validation rules
                        if (array_key_exists($column, $nullable)) {
                            return $table->$type($column, ...$length)->nullable($nullable[$column])->change();
                        }

                        // if no validation rule is provided the column is nullable
                        $table->$type($column, ...$length)->nullable()->change();
                    }
                );

                // set empty strings and 0 to NULL
                DB::statement("UPDATE $tableName SET `$column`=NULL WHERE `$column`=''".
                    ($column == 'parent_id' ? ';' : "and `$column` NOT IN ('0');"));
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
        $columns = collect();

        foreach ($table_info_columns as $column) {
            $hasLength = preg_match('/(?<=\()[\d,]+(?=\))/', $column->Type, $match);

            $columns->put($column->Field, [
                'type' => $column->Type,
                'length' => $hasLength ? explode(',', $match[0]) : [],
                'null' => $column->Null === 'YES' ? true : false,
            ]);
        }

        return $columns->toArray();
    }

    public function getModuleTables()
    {
        if (Str::contains($this->path, base_path('modules'))) {
            return collect(File::files($this->path.'/../../Entities'))
                ->map(function ($file) {
                    return Str::replaceFirst('.php', '', Str::lower($file->getFileName()));
                });
        }

        return self::TABLES;
    }
}
