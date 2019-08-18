<?php

use Illuminate\Database\Schema\Blueprint;

class UpdateDebtAddColumns extends BaseMigration
{
    protected $tablename = 'debt';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->tablename, function (Blueprint $table) {
            // Missing
            $table->string('number')->nullable();
            $table->string('voucher_nr');
            $table->date('due_date')->nullable();
            $table->boolean('cleared');

            // Dunning
            $table->tinyInteger('indicator');
            $table->date('dunning_date')->nullable();
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
            $table->dropColumn([
                'number',
                'voucher_nr',
                'due_date',
                'cleared',
                'dunning_date',
                'indicator',
                ]);
        });
    }
}
