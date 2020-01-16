<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class RelationshipFixes extends BaseMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }

    protected function upFixRelationshipTables(string $tableName, array $columns)
    {
        foreach ($columns as $column) {
            Schema::table($tableName, function (Blueprint $table) use ($column) {
                $table->unsignedInteger($column)->nullable()->change();
            });

            DB::statement("UPDATE `$tableName` SET `$column`=NULL WHERE `$column`=0");
        }
    }

    protected function downFixRelationshipTables(string $tableName, array $columns)
    {
        foreach ($columns as $column) {
            Schema::table($tableName, function (Blueprint $table) use ($column) {
                $table->unsignedInteger($column)->change();
            });

            DB::statement("UPDATE `$tableName` SET `$column`=0 WHERE `$column` is NULL");
        }
    }
}
