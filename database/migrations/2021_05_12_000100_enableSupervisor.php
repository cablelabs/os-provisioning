<?php

class EnableSupervisor extends BaseMigration
{
    public $migrationScope = 'system';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        system('systemctl enable supervisord');
        system('systemctl start supervisord');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        system('systemctl disable supervisord');
        system('systemctl stop supervisord');
    }
}
