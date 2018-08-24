<?php

use Illuminate\Database\Schema\Blueprint;

/*
 * MPR: Modem Positioning Rule
 */
class UpdateModemTableRenameTree extends BaseMigration
{
    // name of the table to create
    protected $tablename = 'modem';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->tablename, function (Blueprint $table) {
            $table->renameColumn('tree_id', 'netelement_id');
        });

        return parent::up();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table($this->tablename, function (Blueprint $table) {
            $table->renameColumn('netelement_id', 'tree_id');
        });
    }
}
