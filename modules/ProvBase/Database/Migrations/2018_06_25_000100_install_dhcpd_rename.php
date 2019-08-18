<?php

class InstallDhcpdRename extends BaseMigration
{
    protected $tablename = '';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $old = '/etc/dhcp/nmsprime';
        $new = '/etc/dhcp-nmsprime';

        // install from git
        if (! file_exists($new)) {
            mkdir($new, 0750, true);
            rename("$old/log.conf", "$new/log.conf");
        }

        if (! file_exists("$new/cmts_gws")) {
            mkdir("$new/cmts_gws", 0750, true);
        }

        // move dhcp config to new folder
        // could be either dhcpd.conf or dhcpd.conf.rpmsave
        $files = glob("$old/dhcpd.conf*");
        if (count($files) == 1) {
            rename($files[0], "$new/dhcpd.conf");
            system("sed -i 's|dhcp/nmsprime|dhcp-nmsprime|' $new/dhcpd.conf");
        }

        system("chown -R apache:dhcpd $new");

        // remove old folder
        exec("rm -rf $old");

        // regenerate config files in new folder
        // check if artisan command can safely be called – in case of a fresh installation with enabled
        // ProvVoip there does not exist a mta table ATM (will be migrated later)
        if (
            (! \Module::collections()->has('ProvVoip'))
            ||
            (Schema::hasTable('mta'))
        ) {
            \Artisan::call('nms:dhcp');
        }

        // reload systemd because path-dhcpd.conf was changed
        system('systemctl daemon-reload');

        // restart dhcpd
        system('systemctl restart dhcpd.service');
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
