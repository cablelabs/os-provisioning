<?php

use Illuminate\Database\Schema\Blueprint;

class restartNMSD extends BaseMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        exec('systemctl restart nmsprimed.service');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
