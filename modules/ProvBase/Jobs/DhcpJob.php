<?php

namespace Modules\ProvBase\Jobs;

use Illuminate\Bus\Queueable;
use Modules\ProvBase\Entities\Modem;
use Modules\ProvBase\Entities\NetGw;
use Illuminate\Queue\SerializesModels;
use Modules\ProvBase\Entities\Endpoint;
use Modules\ProvBase\Entities\ProvBase;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class DhcpJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Global Config part
        $prov = ProvBase::first();
        $prov->makeDdnsConf();
        $prov->make_dhcp_glob_conf();
        $prov->make_dhcp_default_network_conf();

        Modem::make_dhcp_cm_all();
        Modem::create_ignore_cpe_dhcp_file();
        Modem::createDhcpBlockedCpesFile();

        Endpoint::makeDhcp4All();
        Endpoint::makeDhcp6All();

        if (\Module::collections()->has('ProvVoip') && \Schema::hasTable('mta')) {
            \Modules\ProvVoip\Entities\Mta::make_dhcp_mta_all();
        }

        // don't run this command during a new installation
        // this is needed, due to cmts to netgw renaming
        $table = (new \ReflectionClass(NetGw::class))->getDefaultProperties()['table'];
        if (\Schema::hasTable($table)) {
            foreach (NetGw::where('type', 'cmts')->get() as $cmts) {
                $cmts->makeDhcpConf();
            }
        }

        // check if we have to build failover conf
        if (
            (\Module::collections()->has('ProvHA')) &&
            // check if master or slave
            (in_array(config('provha.hostinfo.ownState'), ['master', 'slave']))
        ) {
            \Modules\ProvHA\Entities\ProvHA::makeDhcpFailoverConf();
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
