<?php

use Illuminate\Database\Schema\Blueprint;

class CreateDunningTable extends BaseMigration
{
    protected $tablename = 'dunning';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tablename, function (Blueprint $table) {
            $this->up_table_generic($table);

            /* reminder charges (Mahngebühren) */
            $table->float('fine1', 10, 4);
            $table->float('fine2', 10, 4);
            $table->float('fine3', 10, 4);

            /* Fee for all return debit notes (Rücklastschrift) */
            $table->float('fee', 10, 4);
            // Is fee a total fee or shall it be added to the bank fee of a return debit note
            $table->boolean('total');

            return parent::up();
        });

        // set default values for new installation
        DB::update('INSERT INTO '.$this->tablename.' (fee) VALUES(0);');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop($this->tablename);
    }
}
