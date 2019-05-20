<?php

namespace Modules\provbase\Console;

use Illuminate\Console\Command;
use Modules\ProvBase\Entities\Endpoint;
use Modules\ProvBase\Entities\ProvBase;

class cpeHostnameCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'nms:cpe';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Regenerate CPE hostnames';

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
    public function fire()
    {
        $pw = env('DNS_PASSWORD');
        $domain = ProvBase::first()->domain_name;

        // remove all .cpe.$domain forward and reverse DNS entries
        foreach ([$domain, 'in-addr.arpa'] as $zone) {
            $cmd = shell_exec("dig -tAXFR $zone | grep '\.cpe\.$domain.' | awk '{ print \"update delete\", $1 }'; echo send");
            $handle = popen("/usr/bin/nsupdate -v -l -y dhcpupdate:$pw", 'w');
            fwrite($handle, $cmd);
            pclose($handle);
        }

        // get all active leases
        preg_match_all('/^lease(.*?)(^})/ms', file_get_contents('/var/lib/dhcpd/dhcpd.leases'), $leases);
        // get the required parameters and run named-ddns.sh for every cpe
        foreach ($leases[0] as $lease) {
            if (preg_match('/;\s*binding state active;.*set ip = "([^"]*).*set hw_mac = "([^"]*)/s', $lease, $match)) {
                exec("/etc/named-ddns.sh $match[2] $match[1] 0\n");
            }
        }

        // add forward and reverse DNS entries for all endpoints
        foreach (Endpoint::all() as $endpoint) {
            $endpoint->nsupdate();
        }
    }
}
