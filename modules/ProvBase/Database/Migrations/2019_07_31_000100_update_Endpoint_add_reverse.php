<?php

use Illuminate\Database\Schema\Blueprint;

class UpdateEndpointAddReverse extends BaseMigration
{
    // name of the table to create
    protected $tablename = 'endpoint';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->tablename, function (Blueprint $table) {
            $table->string('add_reverse', 191)->nullable();
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
            $table->dropColumn('add_reverse');
        });
    }
}
