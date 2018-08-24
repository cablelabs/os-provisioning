<?php

use Illuminate\Database\Schema\Blueprint;

/**
 * Updater to add col for the newly required phonebookentry salutation
 *
 * @author Patrick Reichel
 */
class UpdatePhonebookEntryTableAddSalutationCol extends BaseMigration
{
    // name of the table to create
    protected $tablename = 'phonebookentry';

    /**
     * Run the migrations.
     *
     * @author Patrick Reichel
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->tablename, function (Blueprint $table) {
            $table->string('salutation')->after('company');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @author Patrick Reichel
     *
     * @return void
     */
    public function down()
    {
        Schema::table($this->tablename, function (Blueprint $table) {
            $table->dropColumn('salutation');
        });
    }
}
