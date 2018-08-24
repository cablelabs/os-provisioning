<?php

use Illuminate\Database\Schema\Blueprint;

class UpdatePhonebookEntryRenameUsageCol extends BaseMigration
{
    // name of the table to update
    protected $tablename = 'phonebookentry';

    /**
     * Run the migrations - rename usage col to meet structure returned by Telekom
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->tablename, function (Blueprint $table) {
            $table->renameColumn('number_usage', 'usage');
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
            $table->renameColumn('usage', 'number_usage');
        });
    }
}
