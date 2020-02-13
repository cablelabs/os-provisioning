<?php

use Illuminate\Database\Schema\Blueprint;

class UpdateEndpointMakeIpNullable extends BaseMigration
{
    private $tablename = 'endpoint';

    public function up()
    {
        Schema::table($this->tablename, function (Blueprint $table) {
            $table->string('ip')->nullable()->change();
        });

        // set empty strings to NULL
        DB::statement("UPDATE $this->tablename SET `ip`=NULL WHERE `ip`=''");
    }

    public function down()
    {
        DB::statement("UPDATE $this->tablename SET `ip`='' WHERE `ip` IS NULL");

        Schema::table($this->tablename, function (Blueprint $table) {
            $table->string('ip')->nullable(false)->change();
        });
    }
}
