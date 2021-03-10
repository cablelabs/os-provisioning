<?php

class InstallUpdate254To300 extends BaseMigration
{
    protected $tablename = '';
    public $migrationScope = 'system';

    /**
     * Restart GenieACS & nmsprimed after configuration has changed from NMS Prime version 2.5.4 to 3.0.0
     * Remove sessions to rebuild cached sidebar
     *
     * @return void
     */
    public function up()
    {
        exec('systemctl reenable genieacs-{cwmp,fs,nbi,ui}');
        exec('systemctl restart genieacs-{cwmp,fs,nbi,ui}');
        exec('systemctl restart httpd');

        exec("sed -i 's/ExecStart=\/usr\/bin\/php/ExecStart=\/opt\/rh\/rh-php73\/root\/usr\/bin\/php/' /usr/lib/systemd/system/nmsprimed.service");
        exec('systemctl daemon-reload');
        exec('systemctl restart nmsprimed');

        \Artisan::call('nms:radgroupreply-repopulate');

        exec('rm -f storage/framework/sessions/*');
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
