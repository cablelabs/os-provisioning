<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class RelationshipFixesProvBase extends BaseMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // set 0 to NULL for contract
        Schema::table('contract', function (Blueprint $table) {
            foreach (['country_id', 'qos_id', 'next_qos_id'] as $column) {
                $table->unsignedInteger($column)->nullable()->change();
                DB::statement("UPDATE `contract` SET `$column`=NULL WHERE `$column`=0");
            }
        });

        // set 0 to NULL for ippool
        Schema::table('ippool', function (Blueprint $table) {
            $column = 'netgw_id';
            $table->unsignedInteger($column)->nullable()->change();
            DB::statement("UPDATE ippool SET `$column`=NULL WHERE `$column`=0");
        });

        // set 0 to NULL for modem
        Schema::table('modem', function (Blueprint $table) {
            foreach (['country_id', 'qos_id', 'netelement_id'] as $column) {
                $table->unsignedInteger($column)->nullable()->change();
                DB::statement("UPDATE modem SET `$column`=NULL WHERE `$column`=0");
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
        // set NULL ro 0 for contract
        Schema::table('contract', function (Blueprint $table) {
            foreach (['country_id', 'qos_id', 'next_qos_id'] as $column) {
                $table->unsignedInteger($column)->change();
                DB::statement("UPDATE `contract` SET `$column`=0 WHERE `$column` is NULL");
            }
        });

        // set NULL ro 0 for ippool
        Schema::table('ippool', function (Blueprint $table) {
            $column = 'netgw_id';
            $table->unsignedInteger($column)->change();
            DB::statement("UPDATE ippool SET `$column`=0 WHERE `$column` is NULL");
        });

        // set NULL ro 0 for modem
        Schema::table('modem', function (Blueprint $table) {
            foreach (['country_id', 'qos_id', 'netelement_id'] as $column) {
                $table->unsignedInteger($column)->change();
                DB::statement("UPDATE modem SET `$column`=0 WHERE `$column` is NULL");
            }
        });
    }
}
