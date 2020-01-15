<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class RelationshipFixesHfcReq extends BaseMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // set 0 to NULL for netelementtype
        Schema::table('netelementtype', function (Blueprint $table) {
            $column = 'pre_conf_oid_id';
            $table->unsignedInteger($column)->nullable()->change();
            DB::statement("UPDATE netelementtype SET `$column`=NULL WHERE `$column`=0");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // set 0 to NULL for netelementtype
        Schema::table('netelementtype', function (Blueprint $table) {
            $column = 'pre_conf_oid_id';
            $table->unsignedInteger($column)->change();
            DB::statement("UPDATE netelementtype SET `$column`=0 WHERE `$column` IS NULL");
        });
    }
}
