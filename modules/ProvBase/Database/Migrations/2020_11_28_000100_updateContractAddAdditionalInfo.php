<?php

use Illuminate\Database\Schema\Blueprint;

class UpdateContractAddAdditionalInfo extends BaseMigration
{
    protected $tablename = 'contract';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->tablename, function (Blueprint $table) {
            $table->string('additional')->nullable()->default(null);
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
            $table->dropColumn(['additional']);
        });
    }
}
