<?php

use Illuminate\Database\Schema\Blueprint;
use Modules\ProvVoip\Console\EkpCodeDatabaseUpdaterCommand;

class CreateEkpCodeTable extends BaseMigration
{
    // name of the table to create
    protected $tablename = 'ekpcode';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tablename, function (Blueprint $table) {
            $this->up_table_generic($table);

            $table->string('ekp_code', 191)->unique();
            $table->string('company');
        });

        // empty csv hash (to be sure that newly created table will be filled)
        $updater = new EkpCodeDatabaseUpdaterCommand();
        $updater->clear_hash_file();

        // to fill this table call “php artisan provvoip:update_ekp_code_database“

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
