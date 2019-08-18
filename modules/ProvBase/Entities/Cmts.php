<?php

namespace Modules\ProvBase\Entities;

use File;

class Cmts extends \BaseModel
{
    private static $_us_snr_path = 'data/provmon/us_snr';
    // don't put a trailing slash here!
    public static $cmts_include_path = '/etc/dhcp-nmsprime/cmts_gws';

    // The associated SQL table for this Model
    public $table = 'cmts';

    // Add your validation rules here
    public static function rules($id = null)
    {
        return [
            'hostname' => 'required|unique:cmts,hostname,'.$id.',id,deleted_at,NULL',  	// unique: table, column, exception , (where clause)
            'company' => 'required',
        ];
    }

    // Name of View
    public static function view_headline()
    {
        return 'CMTS';
    }

    // View Icon
    public static function view_icon()
    {
        return '<i class="fa fa-server"></i>';
    }

    public static function make_dhcp_conf_all()
    {
        foreach (self::all() as $cmts) {
            $cmts->make_dhcp_conf();
        }
    }

    // AJAX Index list function
    // generates datatable content and classes for model
    public function view_index_label()
    {
        $bsclass = $this->get_bsclass();

        return ['table' => $this->table,
                'index_header' => [$this->table.'.id', $this->table.'.hostname', $this->table.'.ip', $this->table.'.company', $this->table.'.type'],
                'header' =>  $this->hostname,
                'bsclass' => $bsclass,
                'order_by' => ['0' => 'asc'], ];
    }

    public function get_bsclass()
    {
        $bsclass = 'success';

        // TODO: use cmts state value
        if ($this->state == 1) {
            $bsclass = 'warning';
        }
        if ($this->state == 2) {
            $bsclass = 'danger';
        }

        return $bsclass;
    }

    /**
     * BOOT - init cmts observer
     */
    public static function boot()
    {
        parent::boot();

        self::observe(new CmtsObserver);
        self::observe(new \App\SystemdObserver);
    }

    /**
     * Relationships:
     */
    public function ippools()
    {
        return $this->hasMany('Modules\ProvBase\Entities\IpPool');
    }

    public function netelement()
    {
        return $this->hasOne('Modules\HfcReq\Entities\NetElement', 'prov_device_id');
    }

    // returns all objects that are related to a cmts
    public function view_has_many()
    {
        // related IP Pools
        $ret['Edit']['IpPool']['class'] = 'IpPool';
        $ret['Edit']['IpPool']['relation'] = $this->ippools;

        // Routing page
        $this->prep_cmts_config_page();
        $ret['Edit']['Config']['view']['vars'] = ['cb' => $this]; // cb .. CMTS blade
        $ret['Edit']['Config']['view']['view'] = 'provbase::Cmts.overview';

        // rf card page
        $this->prep_rfcard_page();
        $ret['Edit']['Cluster']['view']['vars'] = ['rf' => $this]; // rf .. RF card blade
        $ret['Edit']['Cluster']['view']['view'] = 'provbase::Rfcardblade.overview';
        // uncomment: to use default blade instead
        //$ret['Base']['NetElement']['class'] = 'NetElement';
        //$ret['Base']['NetElement']['relation'] = $this->clusters;

        return $ret;
    }

    /*
     * Return the CMTS config as clear text
     */
    public function get_raw_cmts_config()
    {
        $this->prep_cmts_config_page();
        $view_var = $this;
        $cb = $this;

        if (\View::exists('provbase::Cmtsblade.'.strtolower($view_var->company))) {
            return strip_tags(view('provbase::Cmtsblade.'.strtolower($this->company), compact('cb', 'view_var'))->render());
        }

        return '';
    }

    /*
     * create a cisco encrypted password, like $1$fUW9$EAwpFkkbCTUUK8MpRS1sI0
     *
     * See: https://serverfault.com/questions/26188/code-to-generate-cisco-secret-password-hashes/46399
     *
     * NOTE: dont encrypt if CMTS_SAVE_ENCRYPTED_PASSWORDS is set in env file
     */
    public function create_cisco_encrypt($psw)
    {
        // Dont encrypt password, it is still encrypted
        if (env('CMTS_SAVE_ENCRYPTED_PASSWORDS', false)) {
            return $psw;
        }

        exec('openssl passwd -salt `openssl rand -base64 3` -1 "'.$psw.'"', $output);

        return $output[0];
    }

    /*
     * CMTS Config Page:
     * Prepare Cmts Config Variables
     *
     * They are required in Cmtsblade's
     *
     * NOTE: this will fit 90% of generic installations
     */
    public function prep_cmts_config_page()
    {
        // password section
        $this->enable_secret = $this->create_cisco_encrypt(env('CMTS_ENABLE_SECRET', 'admin'));
        $this->admin_psw = $this->create_cisco_encrypt(env('CMTS_ADMIN_PASSWORD', 'admin'));
        // NOTE: this is quit insecure and should be a different psw that the encrypted ones above!
        $this->vty_psw = env('CMTS_VTY_PASSWORD', 'adminvty');

        // type specific settings
        switch ($this->type) {
            case 'ubr7225':
                $this->interface = 'GigabitEthernet0/1';
                break;

            case 'ubr10k':
                $this->interface = 'GigabitEthernet1/0/0';
                break;

            default:
                $this->interface = 'GigabitEthernet0/1';
                break;
        }

        // get provisioning IP and interface
        $this->prov_ip = ProvBase::first()->provisioning_server;
        exec('ip a | grep '.$this->prov_ip.' | tr " " "\n" | tail -n1', $prov_if);
        $this->prov_if = (isset($prov_if[0]) ? $prov_if[0] : 'eth');

        $this->domain = ProvBase::first()->domain_name;
        $this->router_ip = env('CMTS_DEFAULT_GW', '172.20.3.254');
        $this->netmask = env('CMTS_IP_NETMASK', '255.255.252.0');
        $this->tf_net_1 = env('CMTS_TRANSFER_NET', '172.20.0.0'); // servers with /24
        $this->nat_ip = env('CMTS_NAT_IP', '172.20.0.2'); // second server ip is mostlikely NAT

        $this->snmp_ro = $this->get_ro_community();
        $this->snmp_rw = $this->get_rw_community();

        // Help section: onhover
        $this->enable_secret = '<span title="CMTS_ENABLE_SECRET and CMTS_SAVE_ENCRYPTED_PASSWORDS"><b>'.$this->enable_secret.'</b></span>';
        $this->admin_psw = '<span title="CMTS_ADMIN_PASSWORD and CMTS_SAVE_ENCRYPTED_PASSWORDS"><b>'.$this->admin_psw.'</b></span>';
        $this->vty_psw = '<span title="CMTS_VTY_PASSWORD"><b>'.$this->vty_psw.'</b></span>';
        $this->prov_ip = '<span title="Set in Global Config Page / Provisioning / Provisioning Server IP"><b>'.$this->prov_ip.'</b></span>';
        $this->interface = '<span title="Depending on CMTS Device Company and Type"><b>'.$this->interface.'</b></span>';
        $this->domain = '<span title="Set in Global Config Page / Provisioning / Domain Name"><b>'.$this->domain.'</b></span>';
        $this->router_ip = '<span title="CMTS_DEFAULT_GW"><b>'.$this->router_ip.'</b></span>';
        $this->netmask = '<span title="CMTS_IP_NETMASK"><b>'.$this->netmask.'</b></span>';
        $this->tf_net_1 = '<span title="CMTS_TRANSFER_NET"><b>'.$this->tf_net_1.'</b></span>';
        $this->nat_ip = '<span title="CMTS_NAT_IP"><b>'.$this->nat_ip.'</b></span>';
        $this->snmp_ro = '<span title="Set in CMTS page or Global Config Page / Provisioning if empty in CMTS page"><b>'.$this->snmp_ro.'</b></span>';
        $this->snmp_rw = '<span title="Set in CMTS page or Global Config Page / Provisioning if empty in CMTS page"><b>'.$this->snmp_rw.'</b></span>';
    }

    /*
     * CMTS Config Page:
     * Prepare Cmts Config Variables
     *
     * They are required in Cmtsblade's
     *
     * NOTE: this will fit 90% of generic installations
     */
    public function prep_rfcard_page()
    {
        $netelement = $this->netelement;

        $clusters = [];
        if ($netelement) {
            foreach (\Modules\HfcReq\Entities\NetElement::where('cmts', '=', $netelement->id)->get() as $ne) {
                if ($ne->get_base_netelementtype() == 2) {
                    $clusters[$ne->id] = $ne;
                }
            }
        }

        $this->clusters = $clusters;
    }

    /**
     * Get SNMP read-only community string
     *
     * @author Ole Ernst
     */
    public function get_ro_community()
    {
        if ($this->community_ro) {
            return $this->community_ro;
        } else {
            return ProvBase::first()->ro_community;
        }
    }

    /**
     * Get SNMP read-write community string
     *
     * @author Ole Ernst
     */
    public function get_rw_community()
    {
        if ($this->community_rw) {
            return $this->community_rw;
        } else {
            return ProvBase::first()->rw_community;
        }
    }

    /**
     * Get US SNR of a registered CM
     *
     * @param ip: ip address of cm
     *
     * @author Nino Ryschawy
     */
    public function get_us_snr($ip)
    {
        $fn = self::$_us_snr_path."/$this->id.json";

        if (! \Storage::exists($fn)) {
            \Log::error("Missing Modem US SNR json file of CMTS $this->hostname [$this->id]");

            return;
        }

        $snrs = json_decode(\Storage::get($fn), true);

        if (array_key_exists($ip, $snrs)) {
            return $snrs[$ip];
        }
    }

    /**
     * Store US SNR values for all modems once every 5 minutes
     * this greatly reduces the cpu load on the cmts
     *
     * @author Ole Ernst
     * @author Nino Ryschawy  - D2.0 Extension
     */
    public function store_us_snrs()
    {
        $ret = [];
        $com = $this->get_ro_community();

        snmp_set_valueretrieval(SNMP_VALUE_LIBRARY);
        snmp_set_quick_print(true);

        \Log::debug("CMTS $this->hostname: Store CM US SNRs");

        try {
            try {
                $freq = [];
                foreach (snmp2_real_walk($this->ip, $com, '.1.3.6.1.2.1.10.127.1.1.2.1.2') as $idx => $f) {
                    $freq[last(explode('.', $idx))] = strval($f / 1000000);
                }
                // DOCS-IF3-MIB::docsIf3CmtsCmRegStatusIPv4Addr, ...
                $ips = snmp2_real_walk($this->ip, $com, '.1.3.6.1.4.1.4491.2.1.20.1.3.1.5');
                $snrs = snmp2_real_walk($this->ip, $com, '.1.3.6.1.4.1.4491.2.1.20.1.4.1.4');

                foreach ($ips as $ip_idx => $ip) {
                    // if all hex values of the given ip address can be interpreted as ASCII,
                    // net-snmp won't return as Hex-STRING but STRING, thus we need to adjust
                    // unfortunately via php we can't supply -Ox to force Hex-STRING output
                    if (strlen($ip) == 6) {
                        $ip = bin2hex(trim($ip, '"'));
                    }
                    $ip = long2ip(hexdec($ip));
                    if ($ip == '0.0.0.0') {
                        continue;
                    }
                    $ip_idx = last(explode('.', $ip_idx));

                    foreach ($snrs as $idx => $snr) {
                        if (strpos($idx, $ip_idx) === false) {
                            continue;
                        }
                        $idx = last(explode('.', $idx));

                        $ret[$ip][$freq[$idx]] = $snr / 10;
                    }
                }
            } catch (\Exception $e) {
                if (strpos($e->getMessage(), 'No Such Object available on this agent at this OID') === false) {
                    throw $e;
                }
                // try DOCSIS2.0 - DOCS-IF-MIB::docsIfCmtsCmStatusIpAddress, ...
                $ips = snmp2_real_walk($this->ip, $com, '.1.3.6.1.2.1.10.127.1.3.3.1.3');
                $snrs = snmp2_real_walk($this->ip, $com, '.1.3.6.1.2.1.10.127.1.3.3.1.13');
                $us_idxs = snmp2_real_walk($this->ip, $com, '.1.3.6.1.2.1.10.127.1.3.3.1.5');

                foreach ($ips as $ip_idx => $ip) {
                    if ($ip == '0.0.0.0') {
                        continue;
                    }
                    $ip_idx = last(explode('.', $ip_idx));

                    foreach ($snrs as $idx => $snr) {
                        if (strpos($idx, $ip_idx) === false) {
                            continue;
                        }
                        $idx = last(explode('.', $idx));

                        $us_idx = array_filter($us_idxs, function ($us_idx) use ($idx) {
                            return strpos($us_idx, $idx) !== false;
                        }, ARRAY_FILTER_USE_KEY);
                        $us_idx = last($us_idx);

                        $ret[$ip][$freq[$us_idx]] = $snr / 10;
                    }
                }
            }
        } catch (\Exception $e) {
            // have to catch errors here – throwing an exception results in stopping the console command
            // no other CMTSs will be asked for US values after the first crash
            \Log::error("Cannot get modem US SNR values for CMTS $this->hostname: ".get_class($e).' ('.$e->getMessage().') in '.$e->getFile().':'.$e->getLine());

            return;
        }

        \Storage::put(self::$_us_snr_path."/$this->id.json", json_encode($ret));
    }

    /**
     * Get US modulations of the respective channel ID
     *
     * @param ch_ids: Array of channel IDs
     * @return array of corresponding modulations used (docsIfCmtsModType)
     *
     * @author Ole Ernst
     */
    public function get_us_mods($ch_ids)
    {
        $mods = [];
        // get all channel IDs of the CMTS
        try {
            $idxs = snmprealwalk($this->ip, $this->get_ro_community(), '.1.3.6.1.2.1.10.127.1.1.2.1.1');

            // intersect all channel IDs with the ones used by the modem (supplied as method argument)
            foreach (array_intersect($idxs, $ch_ids) as $key => $val) {
                $key = explode('.', $key);
                // get the modulation profile ID used for this channel
                $mod_prof = snmpwalk($this->ip, $this->get_ro_community(), '.1.3.6.1.2.1.10.127.1.1.2.1.4.'.end($key));
                // get all modulations of this profile
                $mod = snmpwalk($this->ip, $this->get_ro_community(), '.1.3.6.1.2.1.10.127.1.3.5.1.4.'.array_pop($mod_prof));
                // only add the last one, as this is used for user data
                $mods[] = array_pop($mod);
            }
        } catch (\ErrorException $ex) {
            \Log::error('ErrorException in '.__METHOD__.'(): '.$ex->getMessage());
        } catch (\Exception $ex) {
            throw $ex;
        }

        return $mods;
    }

    /**
     * auto generates the dhcp conf file for a specified cmts and
     * adds the appropriate include statement in dhcpd.conf
     *
     * (description is automatically taken by phpdoc)
     *
     * TODO: improve performance by collecting data first and put to file once at the end
     *
     * @author Nino Ryschawy
     */
    public function make_dhcp_conf()
    {
        $file = self::$cmts_include_path."/$this->id.conf";

        if ($this->id == 0) {
            return -1;
        }

        $ippools = $this->ippools;

        // if a cmts doesn't have an ippool the file has to be empty
        if (! $ippools->has('0')) {
            File::put($file, '');
            goto _exit;
        }

        File::put($file, 'shared-network "'.$this->hostname.'"'."\n".'{'."\n");

        foreach ($ippools as $pool) {
            if ($pool->id == 0) {
                continue;
            }

            $subnet = $pool->net;
            $netmask = $pool->netmask;
            $broadcast_addr = $pool->broadcast_ip;
            $range = $pool->get_range();
            $router = $pool->router_ip;
            $type = $pool->type;
            $options = $pool->optional;
            $dns['1'] = $pool->dns1_ip;
            $dns['2'] = $pool->dns2_ip;
            $dns['3'] = $pool->dns3_ip;

            $data = "\n\t".'subnet '.$subnet.' netmask '.$netmask."\n\t".'{';
            $data .= "\n\t\t".'option routers '.$router.';';
            if ($broadcast_addr != '') {
                $data .= "\n\t\t".'option broadcast-address '.$broadcast_addr.';';
            }
            if ($dns['1'] != '' || $dns['2'] != '' || $dns['3'] != '') {
                $data .= "\n\t\toption domain-name-servers ";
                $data_tmp = '';
                foreach ($dns as $ip) {
                    if ($ip != '') {
                        $data_tmp .= "$ip, ";
                    }
                }
                $pos = strrpos($data_tmp, ',');
                $data .= substr_replace($data_tmp, '', $pos, 1).';';
            }

            if ($range) {
                $data .= "\n\n\t\t".'pool'."\n\t\t{\n";
                $data .= $range;

                switch ($type) {
                    case 'CM':
                        $data .= "\n\t\t\t".'allow members of "CM";';
                        $data .= "\n\t\t\t".'deny unknown-clients;';
                        break;

                    case 'CPEPriv':
                        $data .= "\n\t\t\t".'allow members of "Client";';
                        $data .= "\n\t\t\t".'deny members of "Client-Public";';
                        // $data .= "\n\t\t\t".'allow known-clients;';
                        break;

                    case 'CPEPub':
                        $data .= "\n\t\t\t".'allow members of "Client-Public";';
                        // $data .= "\n\t\t\t".'allow unknown-clients;';
                        // $data .= "\n\t\t\t".'allow known-clients;';
                        break;

                    case 'MTA':
                        $data .= "\n\t\t\t".'allow members of "MTA";';
                        // $data .= "\n\t\t\t".'allow known-clients;';
                        break;

                    default:
                        // code...
                        break;
                }

                $data .= "\n\t\t".'}';
            }

            // append additional options
            if ($options) {
                $data .= "\n\n\t\t".$options;
            }

            $data .= "\n\t".'}'."\n";

            $data .= "\n\tsubnet $router netmask 255.255.255.255\n\t{";
            $data .= "\n\t\tallow leasequery;";
            $data .= "\n\t}\n";

            File::append($file, $data);
        }

        File::append($file, '}'."\n");

        _exit:
        self::make_includes();

        // chown for future writes in case this function was called from CLI via php artisan nms:dhcp that changes owner to 'root'
        system('/bin/chown -R apache /etc/dhcp-nmsprime/');
    }

    /**
     * Generate cmts_gws.conf containing includes for the
     * shared-networks of all cmts
     *
     * @author Ole Ernst
     */
    public static function make_includes()
    {
        $path = self::$cmts_include_path;
        $incs = '';
        foreach (self::all() as $cmts) {
            $incs .= "include \"$path/$cmts->id.conf\";\n";
        }
        file_put_contents($path.'.conf', $incs);
    }
}

/**
 * CMTS Observer Class
 * Handles changes on CMTS Gateways
 *
 * can handle   'creating', 'created', 'updating', 'updated',
 *              'deleting', 'deleted', 'saving', 'saved',
 *              'restoring', 'restored',
 */
class CmtsObserver
{
    public static $cmts_tftp_path = '/tftpboot/cmts';

    public function created($cmts)
    {
        if (\Module::collections()->has('ProvMon')) {
            \Artisan::call('nms:cacti', ['--modem-id' => 0, '--cmts-id' => $cmts->id]);
        }
        $cmts->make_dhcp_conf();

        File::put(self::$cmts_tftp_path."/$cmts->id.cfg", $cmts->get_raw_cmts_config());
    }

    public function updated($cmts)
    {
        $cmts->make_dhcp_conf();

        File::put(self::$cmts_tftp_path."/$cmts->id.cfg", $cmts->get_raw_cmts_config());
    }

    public function deleted($cmts)
    {
        File::delete(Cmts::$cmts_include_path."/$cmts->id.conf");
        File::delete(self::$cmts_tftp_path."/$cmts->id.cfg");

        Cmts::make_includes();
    }
}
