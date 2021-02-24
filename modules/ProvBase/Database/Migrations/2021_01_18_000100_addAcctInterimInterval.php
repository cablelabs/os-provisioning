<?php

use Illuminate\Database\Schema\Blueprint;

class addAcctInterimInterval extends BaseMigration
{
    protected $tablename = 'provbase';
    public $migrationScope = 'database';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->tablename, function (Blueprint $table) {
            $table->integer('acct_interim_interval')->unsigned()->default(300);
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
            $table->dropColumn('acct_interim_interval');
        });
    }
}
