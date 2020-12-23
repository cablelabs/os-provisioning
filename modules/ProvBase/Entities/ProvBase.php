<?php

namespace Modules\ProvBase\Entities;

use File;

class ProvBase extends \BaseModel
{
    // The associated SQL table for this Model
    protected $table = 'provbase';

    public $name = 'Provisioning Basic Config';

    protected const DEFAULT_NETWORK_FILE_PATH = '/etc/dhcp-nmsprime/default-network.conf';

    // Don't forget to fill this array
    // protected $fillable = ['provisioning_server', 'ro_community', 'rw_community', 'domain_name', 'notif_mail', 'dhcp_def_lease_time', 'dhcp_max_lease_time', 'startid_contract', 'startid_modem', 'startid_endpoint'];

    // Add your validation rules here
    public function rules()
    {
        return [
            'provisioning_server' => 'required|ip',
            'dhcp_def_lease_time' => 'required|integer|min:1',
            'dhcp_max_lease_time' => 'required|integer|min:1',
            'ppp_session_timeout' => 'required|integer|min:0',
            'max_cpe' => 'required|integer|min:0|max:254',
        ];
    }

    // Name of View
    public static function view_headline()
    {
        return 'Prov Base Config';
    }

    // link title in index view
    public function view_index_label()
    {
        return 'Prov Base';
    }

    // View Icon
    public static function view_icon()
    {
        return '<i class="fa fa-database"></i>';
    }

    /**
     * BOOT - init provbase observer
     */
    public static function boot()
    {
        parent::boot();

        self::observe(new \Modules\ProvBase\Observers\ProvBaseObserver);
        self::observe(new \App\Observers\SystemdObserver);
    }

    /*
     * Return true if $this->prov_ip is online, otherwise false
     * This implies that the Mgmt Interface is setup correctly
     */
    public static function prov_ip_online()
    {
        // Ping: Only check if device is online
        exec('sudo ping -c1 -i0 -w1 '.self::first()->provisioning_server, $ping, $ret);

        return $ret ? false : true;
    }

    /**
     * Create the global configuration file for DHCP Server from Global Config Parameters
     * Set correct Domain Name on Server from GUI (Permissions via sudoers-file needed!!)
     *
     * @author Nino Ryschawy
     */
    public function make_dhcp_glob_conf()
    {
        $file_dhcp_conf = '/etc/dhcp-nmsprime/global.conf';

        $data = 'ddns-domainname "'.$this->domain_name.'.";'."\n";
        $data .= 'option domain-name "'.$this->domain_name.'";'."\n";
        $data .= 'option domain-name-servers '.$this->provisioning_server.";\n";
        $data .= 'default-lease-time '.$this->dhcp_def_lease_time.";\n";
        $data .= 'max-lease-time '.$this->dhcp_max_lease_time.";\n";
        $data .= 'next-server '.$this->provisioning_server.";\n";
        $data .= 'option log-servers '.$this->provisioning_server.";\n";
        $data .= 'option time-servers '.$this->provisioning_server.";\n";
        $data .= 'option time-offset '.date('Z').";\n";

        $data .= "\n# zone\nzone ".$this->domain_name." {\n\tprimary 127.0.0.1;\n\tkey dhcpupdate;\n}\n";
        $data .= "\n# reverse zone\nzone in-addr.arpa {\n\tprimary 127.0.0.1;\n\tkey dhcpupdate;\n}\n";

        if (\Module::collections()->has('ProvVoip') && \Schema::hasTable('provvoip')) {
            // second domain for mta's if existent
            $mta_domain = \Modules\ProvVoip\Entities\ProvVoip::first()->mta_domain;
            $data .= $mta_domain ? "\n# zone for voip devices\nzone ".$mta_domain." {\n\tprimary ".$this->provisioning_server.";\n\tkey dhcpupdate;\n}\n" : '';
        }

        // provisioning server hostname encoding for dhcp
        $fqdn = exec('hostname');
        $hostname = '';
        $dhcp_fqdn = '';

        if (($pos = strpos($fqdn, $this->domain_name)) !== false) {
            // correct domain name already set
            if ($pos == 0) {
                throw new \Exception('Hostname of Server not Set! Please specify a hostname via command line first!', 1);
            }
        } else {
            // Set correct fully qualified domain name for server - we expect the hostname to be the first word in previous fqdn
            $hostname = explode('.', $fqdn);

            if (! isset($hostname[0])) {
                throw new \Exception('Hostname of Server not Set! Please specify a hostname via command line first!', 1);
            } else {
                $hostname = $hostname[0];
            }

            $fqdn = $hostname.'.'.$this->domain_name;

            system('sudo hostnamectl set-hostname '.escapeshellarg($fqdn), $ret);

            if ($ret != 0) {
                throw new \Exception('Could not Set FQDN. No Permission? Please add actual version of laravel sudoers file to /etc/sudoers.d/!', 1);
            }
        }

        $arr = explode('.', $fqdn);

        // encode - every word needs a backslash and it's length as octal number (with leading zero's - up to 3 numbers) in front of itself
        foreach ($arr as $value) {
            $nr = strlen($value);
            $nr = decoct((int) $nr);
            $dhcp_fqdn .= sprintf("\%'.03d%s", $nr, $value);
        }
        $dhcp_fqdn .= '\\000';

        $data .= "\n# CLASS Specs for CM, MTA, CPE\n";
        $data .= 'class "CM" {'."\n\t".'match if (substring(option vendor-class-identifier,0,6) = "docsis");'."\n\toption ccc.dhcp-server-1 0.0.0.0;\n\tddns-updates on;\n}\n\n";
        $data .= 'class "MTA" {'."\n\t".'match if (substring(option vendor-class-identifier,0,4) = "pktc");'."\n\t".'option ccc.provision-server 0 "'.$dhcp_fqdn.'"; # number of letters before every through dot seperated word'."\n\t".'option ccc.realm 05:42:41:53:49:43:01:31:00;  # BASIC.1'."\n\tddns-updates on;\n}\n\n";
        $data .= 'class "Client" {'."\n\t".'match if ((substring(option vendor-class-identifier,0,6) != "docsis") and (substring(option vendor-class-identifier,0,4) != "pktc"));'."\n\t".'spawn with binary-to-ascii(16, 8, ":", substring(option agent.remote-id, 0, 6)); # create a sub-class automatically'."\n\t".'lease limit 4; # max 4 private cpe per cm'."\n}\n\n";
        $data .= 'class "Client-Public" {'."\n\t".'match if ((substring(option vendor-class-identifier,0,6) != "docsis") and (substring(option vendor-class-identifier,0,4) != "pktc"));'."\n\t".'match pick-first-value (option agent.remote-id);'."\n\t".'lease limit 4; # max 4 public cpe per cm'."\n}\n\n";
        $data .= 'class "blocked" {'."\n\t".'match if ((substring(option vendor-class-identifier,0,6) != "docsis") and (substring(option vendor-class-identifier,0,4) != "pktc"));'."\n\tmatch pick-first-value (option agent.remote-id);\n\tdeny booting;\n}\n\n";

        File::put($file_dhcp_conf, $data);
    }

    /**
     * Create the default shared-network based on the provisioning server ip
     * address, so that dhcpd knows on which interface to listen
     *
     * @author Ole Ernst
     */
    public function make_dhcp_default_network_conf()
    {
        $sub = new \IPv4\SubnetCalculator($this->provisioning_server, 22);
        $net = $sub->getNetworkPortion();
        $mask = $sub->getSubnetMask();

        $data = "shared-network ETHERNET\n{\n\tsubnet $net netmask $mask\n\t{\n\t\tdeny booting;\n\t}\n}\n";

        return file_put_contents(self::DEFAULT_NETWORK_FILE_PATH, $data, LOCK_EX);
    }
}
