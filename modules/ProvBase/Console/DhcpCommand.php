<?php

namespace Modules\ProvBase\Console;

use Illuminate\Console\Command;
use Modules\ProvBase\Entities\Modem;
use Modules\ProvBase\Entities\NetGw;
use Modules\ProvBase\Entities\Endpoint;
use Modules\ProvBase\Entities\ProvBase;

class DhcpCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'nms:dhcp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'make the DHCP config';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command - Create global Config & all Entries for Modems, Endpoints & Mtas to get an IP from Server
     *
     * @return mixed
     */
    public function handle()
    {
        // Global Config part
        $prov = ProvBase::first();
        $prov->make_dhcp_glob_conf();
        $prov->make_dhcp_default_network_conf();

        echo 'Create '.Modem::CONF_FILE_PATH."...\n";
        Modem::make_dhcp_cm_all();
        Modem::create_ignore_cpe_dhcp_file();
        Modem::createDhcpBlockedCpesFile();

        echo "Create host/endpoint DHCP config file(s)...\n";
        Endpoint::makeDhcp4All();
        Endpoint::makeDhcp6All();

        if (\Module::collections()->has('ProvVoip') && \Schema::hasTable('mta')) {
            echo 'Create '.\Modules\ProvVoip\Entities\Mta::CONF_FILE_PATH."...\n";
            \Modules\ProvVoip\Entities\Mta::make_dhcp_mta_all();
        }

        // don't run this command during a new installation
        // this is needed, due to cmts to netgw renaming
        $table = (new \ReflectionClass(NetGw::class))->getDefaultProperties()['table'];
        if (\Schema::hasTable($table)) {
            echo "Create NetGw DHCP config file(s)...\n";
            foreach (NetGw::where('type', 'cmts')->get() as $cmts) {
                $cmts->makeDhcpConf();
            }
        }

        // Restart dhcp server
        $dir = storage_path('systemd/');
        if (! is_dir($dir)) {
            mkdir($dir, 0700, true);
            chown($dir, 'apache');
        }
        touch($dir.'dhcpd');

        system('/usr/bin/chown -R apache /etc/dhcp-nmsprime/ /etc/kea/');
    }
}
