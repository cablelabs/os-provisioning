<?php

class InstallSetFsHostname extends BaseMigration
{
    protected $tablename = '';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // GenieACS > 1.1.3 uses environment variables instead of config.json
        return;

        $filename = '/lib/node_modules/genieacs/config/config.json';
        $conf = json_decode(file_get_contents($filename));

        $host = explode('.', gethostname())[0];
        $domain = \Modules\ProvBase\Entities\ProvBase::first()->domain_name;
        $conf->FS_HOSTNAME = "$host.$domain";

        file_put_contents($filename, json_encode($conf));
        exec('systemctl restart genieacs-fs.service');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // GenieACS > 1.1.3 uses environment variables instead of config.json
        return;

        $filename = '/lib/node_modules/genieacs/config/config.json';
        $conf = json_decode(file_get_contents($filename));

        unset($conf->FS_HOSTNAME);

        file_put_contents($filename, json_encode($conf));
        exec('systemctl restart genieacs-fs.service');
    }
}
