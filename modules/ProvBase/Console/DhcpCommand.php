<?php
/**
 * Copyright (c) NMS PRIME GmbH ("NMS PRIME Community Version")
 * and others – powered by CableLabs. All rights reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at:
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Modules\ProvBase\Console;

use Illuminate\Console\Command;
use Modules\ProvBase\Entities\Endpoint;
use Modules\ProvBase\Entities\Modem;
use Modules\ProvBase\Entities\NetGw;
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

        echo "Build modem related files ...\n";
        Modem::make_dhcp_cm_all();
        echo "Build CPE related files ...\n";
        Modem::create_ignore_cpe_dhcp_file();
        Modem::createDhcpBlockedCpesFile();

        Endpoint::makeDhcp4All();
        Endpoint::makeDhcp6All();

        if (\Module::collections()->has('ProvVoip') && \Schema::hasTable('mta')) {
            echo "Build MTA related files ...\n";
            \Modules\ProvVoip\Entities\Mta::make_dhcp_mta_all();
        }

        // don't run this command during a new installation
        // this is needed, due to cmts to netgw renaming
        $table = (new \ReflectionClass(NetGw::class))->getDefaultProperties()['table'];
        if (\Schema::hasTable($table)) {
            echo "Build NetGw related files ...\n";
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
