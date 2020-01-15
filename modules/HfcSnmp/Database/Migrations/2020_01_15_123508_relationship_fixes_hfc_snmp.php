<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class RelationshipFixesHfcSnmp extends BaseMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // set 0 to NULL for indices
        Schema::table('indices', function (Blueprint $table) {
            foreach (['netelement_id', 'parameter_id'] as $column) {
                $table->unsignedInteger($column)->nullable()->change();
                DB::statement("UPDATE indices SET `$column`=NULL WHERE `$column`=0");
            }
        });

        // set 0 to NULL for parameter
        Schema::table('parameter', function (Blueprint $table) {
            foreach (['oid_id', 'netelementtype_id'] as $column) {
                $table->unsignedInteger($column)->nullable()->change();
                DB::statement("UPDATE parameter SET `$column`=NULL WHERE `$column`=0");
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // set NULL ro 0 for indices
        Schema::table('indices', function (Blueprint $table) {
            foreach (['netelement_id', 'parameter_id'] as $column) {
                $table->unsignedInteger($column)->change();
                DB::statement("UPDATE `indices` SET `$column`=0 WHERE `$column` is NULL");
            }
        });

        // set NULL ro 0 for parameter
        Schema::table('parameter', function (Blueprint $table) {
            foreach (['oid_id', 'netelementtype_id'] as $column) {
                $table->unsignedInteger($column)->change();
                DB::statement("UPDATE parameter SET `$column`=0 WHERE `$column` is NULL");
            }
        });
    }
}
