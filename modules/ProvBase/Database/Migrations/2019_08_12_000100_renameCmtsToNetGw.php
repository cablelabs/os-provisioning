<?php

use Illuminate\Database\Schema\Blueprint;

class RenameCmtsToNetGw extends BaseMigration
{
    protected $tablename = 'cmts';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->tablename, function (Blueprint $table) {
            $table->renameColumn('type', 'series');
        });

        Schema::table($this->tablename, function (Blueprint $table) {
            $table->enum('type', ['cmts', 'bras']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table($this->tablename, function (Blueprint $table) {
            $table->dropColumn('type');
        });

        Schema::table($this->tablename, function (Blueprint $table) {
            $table->renameColumn('series', 'type');
        });
    }
}
