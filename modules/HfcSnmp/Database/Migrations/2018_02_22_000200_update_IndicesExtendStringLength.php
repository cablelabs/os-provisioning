<?php

use Illuminate\Database\Schema\Blueprint;

/**
 * This is used for Specifying Indices for a Parameter of a NetElement
 */
class UpdateIndicesExtendStringLength extends BaseMigration
{
    // name of the table to create
    protected $tablename = 'indices';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->tablename, function (Blueprint $table) {
            $table->string('indices', 1024)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Dont revert as we need this already
    }
}
