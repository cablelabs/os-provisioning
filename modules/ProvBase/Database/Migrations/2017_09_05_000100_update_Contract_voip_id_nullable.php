<?php

use Illuminate\Database\Migrations\Migration;

class UpdateContractVoipIdNullable extends Migration
{
    // name of the table to create
    protected $tablename = 'contract';

    /**
     * Run the migrations.
     * For using the envia TEL API we need some changes in storing the contracts data.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE '.$this->tablename.' MODIFY voip_id INTEGER UNSIGNED NULL DEFAULT NULL;');
        DB::statement('ALTER TABLE '.$this->tablename.' MODIFY next_voip_id INTEGER UNSIGNED NULL DEFAULT NULL;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('UPDATE '.$this->tablename.' SET voip_id = 0 WHERE voip_id = NULL;');
        DB::statement('UPDATE '.$this->tablename.' SET next_voip_id = 0 WHERE next_voip_id NULL;');

        DB::statement('ALTER TABLE '.$this->tablename.' MODIFY voip_id INTEGER UNSIGNED NOT NULL;');
        DB::statement('ALTER TABLE '.$this->tablename.' MODIFY next_voip_id INTEGER UNSIGNED NOT NULL;');
    }
}
