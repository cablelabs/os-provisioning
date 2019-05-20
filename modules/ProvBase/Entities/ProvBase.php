<?php

namespace Modules\ProvBase\Entities;

use File;

class ProvBase extends \BaseModel
{
    // The associated SQL table for this Model
    protected $table = 'provbase';

    public $name = 'Provisioning Basic Config';

    const DEFAULT_NETWORK_FILE_PATH = '/etc/dhcp-nmsprime/default-network.conf';

    // Don't forget to fill this array
    // protected $fillable = ['provisioning_server', 'ro_community', 'rw_community', 'domain_name', 'notif_mail', 'dhcp_def_lease_time', 'dhcp_max_lease_time', 'startid_contract', 'startid_modem', 'startid_endpoint'];

    // Add your validation rules here
    public static function rules($id = null)
    {
        return [
            'provisioning_server' => 'ip',
            // TODO: Add max_cpe rule when validation errors are displayed again
            // 'max_cpe' => 'numeric|min:1|max:254',
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

        self::observe(new ProvBaseObserver);
        self::observe(new \App\SystemdObserver);
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
                throw new Exception('Hostname of Server not Set! Please specify a hostname via command line first!', 1);
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
        $data .= 'class "Client" {'."\n\t".'match if ((substring(option vendor-class-identifier,0,6) != "docsis") and (substring(option vendor-class-identifier,0,4) != "pktc"));'."\n\t".'spawn with option agent.remote-id; # create a sub-class automatically'."\n\t".'lease limit 4; # max 4 private cpe per cm'."\n}\n\n";
        $data .= 'class "Client-Public" {'."\n\t".'match if ((substring(option vendor-class-identifier,0,6) != "docsis") and (substring(option vendor-class-identifier,0,4) != "pktc"));'."\n\t".'match pick-first-value (option agent.remote-id);'."\n\t".'lease limit 4; # max 4 public cpe per cm'."\n}\n\n";

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

/**
 * ProvBase Observer Class
 * Handles changes on ProvBase Gateways
 *
 * can handle   'creating', 'created', 'updating', 'updated',
 *              'deleting', 'deleted', 'saving', 'saved',
 *              'restoring', 'restored',
 */
class ProvBaseObserver
{
    public function updated($model)
    {
        $model->make_dhcp_glob_conf();

        $changes = $model->getDirty();

        // create new CPE ignore file
        if (array_key_exists('multiple_provisioning_systems', $changes)) {
            Modem::create_ignore_cpe_dhcp_file();
        }

        // recreate default network, if provisioning server ip address has been changed
        if (array_key_exists('provisioning_server', $changes)) {
            $model->make_dhcp_default_network_conf();
        }

        // re-evaluate all qos rate_max_help fields if one or both coefficients were changed
        if (multi_array_key_exists(['ds_rate_coefficient', 'us_rate_coefficient'], $changes)) {
            $pb = ProvBase::first();
            foreach (Qos::all() as $qos) {
                $qos->ds_rate_max_help = $qos->ds_rate_max * 1000 * 1000 * $pb->ds_rate_coefficient;
                $qos->us_rate_max_help = $qos->us_rate_max * 1000 * 1000 * $pb->us_rate_coefficient;
                $qos->save();
            }
        }

        // build all Modem Configfiles via Job as this will take a long time
        if (multi_array_key_exists(['ds_rate_coefficient', 'us_rate_coefficient', 'max_cpe'], $changes)) {
            \Queue::push(new \Modules\ProvBase\Console\configfileCommand(0, 'cm'));
        }

        if (array_key_exists('ro_community', $changes)) {
            // update cacti database: replace the original snmp ro_community with the new one
            \DB::connection('mysql-cacti')
                ->table('host')
                ->where('snmp_community', $model->getOriginal('ro_community'))
                ->update(['snmp_community' => $model->ro_community]);
        }

        if (array_key_exists('domain_name', $changes)) {
            // update cacti database: replace the original domain_name with the new one
            \DB::connection('mysql-cacti')
                ->table('host')
                ->where('hostname', 'like', "cm-%.{$model->getOriginal('domain_name')}")
                ->update(['hostname' => \DB::raw("REPLACE(hostname, '{$model->getOriginal('domain_name')}', '$model->domain_name')")]);

            // adjust named config and restart it
            $sed_file = storage_path('app/tmp/update-domain.sed');
            file_put_contents($sed_file, "s/zone \"{$model->getOriginal('domain_name')}\" IN/zone \"$model->domain_name\" IN/g");
            exec("sudo sed -i -f $sed_file /etc/named-nmsprime.conf");

            file_put_contents($sed_file, "s/{$model->getOriginal('domain_name')}/$model->domain_name/g");
            exec('sudo rndc sync -clean');
            exec("sudo sed -i -f $sed_file /var/named/dynamic/nmsprime.test.zone");

            exec('sudo systemctl restart named.service');
            unlink($sed_file);
        }
    }
}
