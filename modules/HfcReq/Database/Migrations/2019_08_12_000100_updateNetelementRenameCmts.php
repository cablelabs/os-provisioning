<?php

use Illuminate\Database\Schema\Blueprint;

class UpdateNetelementRenameCmts extends BaseMigration
{
    // name of the table to create
    protected $tablename = 'netelement';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->tablename, function (Blueprint $table) {
            $table->renameColumn('cmts', 'net_gw_id');
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
            $table->renameColumn('net_gw_id', 'cmts');
        });
    }
}
