<?php

use Illuminate\Database\Schema\Blueprint;

class RenameCmtsToNetGw extends BaseMigration
{
    protected $tablename = 'netgws';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('cmts', $this->tablename);

        Schema::table($this->tablename, function (Blueprint $table) {
            $table->renameColumn('type', 'series');
        });

        Schema::table($this->tablename, function (Blueprint $table) {
            $table->enum('type', ['cmts', 'bras']);
        });

        Schema::table('ippool', function (Blueprint $table) {
            $table->renameColumn('cmts_id', 'net_gw_id');
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

        Schema::rename($this->tablename, 'cmts');

        Schema::table('ippool', function (Blueprint $table) {
            $table->renameColumn('net_gw_id', 'cmts_id');
        });
    }
}
