<?php

use Illuminate\Database\Schema\Blueprint;

class UpdateProvVoipTableAddMtaDomain extends \BaseMigration
{
    // name of the table to create
    protected $tablename = 'provvoip';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->tablename, function (Blueprint $table) {
            $table->string('mta_domain');
        });

        $this->set_fim_fields([
            'mta_domain',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table($this->tablename, function (Blueprint $table) {
            $table->dropColumn('mta_domain');
        });

        $this->set_fim_fields([]);
    }
}
