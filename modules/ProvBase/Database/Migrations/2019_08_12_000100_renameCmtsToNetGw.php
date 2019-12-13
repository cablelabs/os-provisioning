<?php

use Illuminate\Database\Schema\Blueprint;

class RenameCmtsToNetGw extends BaseMigration
{
    protected $tablename = 'netgw';

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
            $table->string('type')->default('cmts');
        });

        Schema::table('ippool', function (Blueprint $table) {
            $table->renameColumn('cmts_id', 'netgw_id');
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
            $table->renameColumn('netgw_id', 'cmts_id');
        });
    }
}
