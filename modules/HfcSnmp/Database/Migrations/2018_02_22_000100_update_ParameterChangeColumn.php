<?php

use Illuminate\Database\Schema\Blueprint;

/**
 * Make third_dimension Column not nullable for better use in SnmpController
 */
class UpdateParameterChangeColumn extends BaseMigration
{
    // name of the table to create
    protected $tablename = 'parameter';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->tablename, function (Blueprint $table) {
            $table->boolean('third_dimension')->nullable(false)->default(0)->change();
            $table->boolean('diff_param')->nullable(false)->default(0)->change();
        });

        DB::table($this->tablename)->whereNull('third_dimension')->update(['third_dimension' => 0]);
        DB::table($this->tablename)->whereNull('diff_param')->update(['third_dimension' => 0]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Dont revert as this assumption is already needed but missing
    }
}
