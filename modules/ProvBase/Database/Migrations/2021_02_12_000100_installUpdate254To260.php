<?php

class InstallUpdate254To260 extends BaseMigration
{
    protected $tablename = '';
    public $migrationScope = 'system';

    /**
     * Restart GenieACS after configuration has changed from NMS Prime version 2.5.4 to 2.6.0
     * Remove sessions to rebuild cached sidebar
     *
     * @return void
     */
    public function up()
    {
        exec('systemctl reenable genieacs-{cwmp,fs,nbi,ui}');
        exec('systemctl restart genieacs-{cwmp,fs,nbi,ui}');
        exec('systemctl restart httpd');

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
