<?php

class AddTR69Device extends BaseMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE configfile MODIFY COLUMN device ENUM('cm', 'mta', 'tr069') NOT NULL");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE configfile MODIFY COLUMN device ENUM('cm', 'mta')");
    }
}
