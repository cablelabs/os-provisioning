<?php

class InstallDisableRadiusDetailLogging extends BaseMigration
{
    protected $tablename = '';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        exec("sed -i 's/^\s*detail/#\tdetail/' /etc/raddb/sites-enabled/default");
        exec('systemctl restart radiusd.service');
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
