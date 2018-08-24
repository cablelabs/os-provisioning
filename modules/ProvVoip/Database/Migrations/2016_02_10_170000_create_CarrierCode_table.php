<?php

use Illuminate\Database\Schema\Blueprint;
use Modules\ProvVoip\Console\CarrierCodeDatabaseUpdaterCommand;

class CreateCarrierCodeTable extends BaseMigration
{
    // name of the table to create
    protected $tablename = 'carriercode';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tablename, function (Blueprint $table) {
            $this->up_table_generic($table);

            $table->string('carrier_code', 4)->unique();
            $table->string('company');
        });

        DB::update('INSERT INTO '.$this->tablename." (carrier_code, company) VALUES('0', '-');");

        // empty csv hash (if exists; to be sure that newly created table will be filled)
        $updater = new CarrierCodeDatabaseUpdaterCommand();
        $updater->clear_hash_file();

        // to fill this table call “php artisan provvoip:update_carrier_code_database“

        return parent::up();
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
