<?php

namespace Modules\ProvBase\Console;

use Illuminate\Console\Command;
use Modules\ProvBase\Entities\Endpoint;
use Modules\ProvBase\Entities\ProvBase;

class CpeHostnameCommand extends Command
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
     * Execute the console command
     *
     * @return mixed
     */
    public function handle()
    {
        $provbase = ProvBase::first();
        $domain = $provbase->domain_name;

        $zones = array_map(function ($zone) {
            return basename($zone, '.zone');
        }, glob('/var/named/dynamic/*in-addr.arpa.zone'));

        $zones[] = $domain;

        // detect servers to be updated
        $servers = [];
        if (! \Module::collections()->has('ProvHA')) {
            $servers['127.0.0.1'] = $provbase->dns_password;
        } else {
            $servers['127.0.0.1'] = $provbase->provhaOwnDnsPw;
            $servers[$provbase->provhaPeerIp] = $provbase->provhaPeerDnsPw;
        }

        // remove all .cpe.$domain forward and reverse DNS entries
        foreach ($zones as $zone) {
            foreach ($servers as $server => $password) {
                $cmd = "server $server\n";
                $cmd .= shell_exec("dig -tAXFR $zone | grep '\.cpe\.$domain.' | awk '{ print \"update delete\", $1 }'; echo send");
                $handle = popen("/usr/bin/nsupdate -v -y dhcpupdate:$password", 'w');
                fwrite($handle, $cmd);
                pclose($handle);

                $log = [
                    '',
                    '--------------------------------------------------------------------------------',
                    date('c'),
                    __METHOD__,
                    $cmd,
                ];
                try {
                    file_put_contents($provbase::NSUPDATE_LOGFILE, implode("\n", $log), FILE_APPEND);
                } catch (\Exception $ex) {
                    $msg = 'Could not write to logfile '.$provbase::NSUPDATE_LOGFILE;
                    $this->addAboveMessage($msg, 'error');
                    \Log::error($msg);
                }
            }
        }

        // get all leases
        preg_match_all('/^lease(.*?)(^})/ms', file_get_contents('/var/lib/dhcpd/dhcpd.leases'), $leases);

        // get the required parameters and run named-ddns.sh for every public cpe
        foreach ($leases[0] as $lease) {
            // can't rely on the order in leases – check each relevant line individually
            if (preg_match('/;\s*binding state active;/s', $lease, $match)) {
                $ip = null;
                $mac = null;
                if (preg_match('/.*set ip = "([^"]*).*/s', $lease, $match)) {
                    $ip = $match[1];
                    $octets = explode('.', $ip);
                    if ((! filter_var($match[1], FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE)) ||
                        ($octets[0] == 100 && $octets[1] >= 64 && $octets[1] <= 127)) {
                        // skip private and NAT IPs
                        continue;
                    }
                }
                if (preg_match('/.*set hw_mac = "([^"]*).*/s', $lease, $match)) {
                    $mac = $match[1];
                }
                if ($ip && $mac) {
                    exec("/etc/named-ddns.sh $mac $ip 0\n");
                }
            }
        }

        // add forward and reverse DNS entries for all endpoints
        foreach (Endpoint::all() as $endpoint) {
            $endpoint->nsupdate();
        }
    }
}
