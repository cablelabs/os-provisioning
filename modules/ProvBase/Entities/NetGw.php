<?php

namespace Modules\ProvBase\Entities;

use File;
use App\Sla;

class NetGw extends \BaseModel
{
    public const TYPES = ['cmts', 'bras', 'olt', 'dslam'];
    // don't put a trailing slash here!
    public const NETGW_INCLUDE_PATH = '/etc/dhcp-nmsprime/cmts_gws';
    protected const US_SNR_PATH = 'data/provmon/us_snr';

    // The associated SQL table for this Model
    public $table = 'netgw';

    // Attributes
    public $guarded = ['formatted_support_state', 'nas_secret'];
    protected $appends = ['formatted_support_state'];

    // Add your validation rules here
    public function rules()
    {
        $id = $this->id;

        $types = implode(self::TYPES, ',');

        return [
            'hostname' => 'required|unique:netgw,hostname,'.$id.',id,deleted_at,NULL',  	// unique: table, column, exception , (where clause)
            'company' => 'required',
            'type' => "required|in:$types",
            'coa_port' => 'nullable|numeric|min:1|max:65535',
        ];
    }

    // Name of View
    public static function view_headline()
    {
        return 'NetGws';
    }

    // View Icon
    public static function view_icon()
    {
        return '<i class="fa fa-server"></i>';
    }

    public static function make_dhcp_conf_all()
    {
        foreach (self::where('type', 'cmts')->get() as $cmts) {
            $cmts->make_dhcp_conf();
        }
    }

    // AJAX Index list function
    // generates datatable content and classes for model
    public function view_index_label()
    {
        $bsclass = $this->get_bsclass();

        $ret = ['table' => $this->table,
            'index_header' => [$this->table.'.id', $this->table.'.hostname', 'type', $this->table.'.ip', $this->table.'.company', $this->table.'.series'],
            'header' =>  $this->hostname,
            'bsclass' => $bsclass,
            'order_by' => ['0' => 'asc'], ];

        if (Sla::firstCached()->valid()) {
            $ret['index_header'][] = $this->table.'.support_state';
            $ret['edit']['support_state'] = 'getSupportState';
            $ret['raw_columns'][] = 'support_state';
        }

        return $ret;
    }

    public function get_bsclass()
    {
        $bsclass = 'success';

        // TODO: use netgw state value
        if ($this->state == 1) {
            $bsclass = 'warning';
        }
        if ($this->state == 2) {
            $bsclass = 'danger';
        }

        return $bsclass;
    }

    /**
     * Return Fontawesome emoji class, and Bootstrap text color
     * @return array
     */
    public function getFaSmileClass()
    {
        switch ($this->support_state) {
            case 'full-support':      $faClass = 'fa-smile-o'; $bsClass = 'success'; break;
            case 'verifying':         $faClass = 'fa-meh-o'; $bsClass = 'warning'; break;
            case 'restricted':         $faClass = 'fa-meh-o'; $bsClass = 'success'; break;
            case 'not-supported':     $faClass = 'fa-frown-o'; $bsClass = 'danger'; break;
            default: $faClass = 'fa-smile'; $bsClass = 'success'; break;
        }

        return ['fa-class'=> $faClass, 'bs-class'=> $bsClass];
    }

    public function getSupportState()
    {
        return $this->formatted_support_state." <i class='pull-right fa fa-2x ".$this->getFaSmileClass()['fa-class'].' text-'.$this->getFaSmileClass()['bs-class']."'></i>";
    }

    /**
     * Formatted attribute of support state.
     * @return string
     */
    public function getFormattedSupportStateAttribute()
    {
        return ucfirst(str_replace('-', ' ', $this->support_state));
    }

    /**
     * BOOT - init netgw observer
     */
    public static function boot()
    {
        parent::boot();

        self::observe(new NetGwObserver);
        self::observe(new \App\SystemdObserver);
    }

    /**
     * Relationships:
     */
    public function ippools()
    {
        return $this->hasMany(IpPool::class, 'netgw_id');
    }

    public function allBrasIpPools()
    {
        $ids = [];

        // show all BRAS ippools for every BRAS
        if ($this->type == 'bras') {
            $ids = self::join('ippool', 'ippool.netgw_id', 'netgw.id')
                ->where('netgw.type', $this->type)
                ->whereNull('netgw.deleted_at')
                ->whereNull('ippool.deleted_at')
                ->pluck('ippool.id');
        }

        return $this->ippools()->orWhereIn('id', $ids);
    }

    public function netelement()
    {
        return $this->hasOne(\Modules\HfcReq\Entities\NetElement::class, 'prov_device_id');
    }

    public function nas()
    {
        return $this->hasOne(Nas::class, 'shortname');
    }

    // returns all objects that are related to a netgw
    public function view_has_many()
    {
        // related IP Pools
        $ret['Edit']['IpPool']['class'] = 'IpPool';
        $ret['Edit']['IpPool']['relation'] = $this->ippools;

        if ($this->type == 'bras') {
            $ret['Edit']['IpPool']['relation'] = $this->allBrasIpPools;
        }

        // Routing page
        $this->prep_netgw_config_page();
        $ret['Edit']['Config']['view']['vars'] = ['cb' => $this]; // cb .. NetGw blade
        $ret['Edit']['Config']['view']['view'] = 'provbase::NetGw.overview';

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
     * Return the NetGw config as clear text
     */
    public function get_raw_netgw_config()
    {
        $this->prep_netgw_config_page();
        $view_var = $this;
        $cb = $this;

        if (\View::exists('provbase::NetGwBlade.'.strtolower($view_var->company))) {
            return strip_tags(view('provbase::NetGwBlade.'.strtolower($this->company), compact('cb', 'view_var'))->render());
        }

        return '';
    }

    /*
     * create a cisco encrypted password, like $1$fUW9$EAwpFkkbCTUUK8MpRS1sI0
     *
     * See: https://serverfault.com/questions/26188/code-to-generate-cisco-secret-password-hashes/46399
     *
     * NOTE: dont encrypt if NETGW_SAVE_ENCRYPTED_PASSWORDS is set in env file
     */
    public function create_cisco_encrypt($psw)
    {
        // Dont encrypt password, it is still encrypted
        if (env('NETGW_SAVE_ENCRYPTED_PASSWORDS', false)) {
            return $psw;
        }

        exec('openssl passwd -salt `openssl rand -base64 3` -1 "'.$psw.'"', $output);

        return $output[0];
    }

    /*
     * NetGw Config Page:
     * Prepare NetGw Config Variables
     *
     * They are required in NetGwBlade's
     *
     * NOTE: this will fit 90% of generic installations
     */
    public function prep_netgw_config_page()
    {
        // password section
        $this->enable_secret = $this->create_cisco_encrypt(env('NETGW_ENABLE_SECRET', 'admin'));
        $this->admin_psw = $this->create_cisco_encrypt(env('NETGW_ADMIN_PASSWORD', 'admin'));
        // NOTE: this is quit insecure and should be a different psw that the encrypted ones above!
        $this->vty_psw = env('NETGW_VTY_PASSWORD', 'adminvty');

        // series specific settings
        switch ($this->series) {
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
        $conf = ProvBase::first();
        $this->prov_ip = $conf->provisioning_server;
        exec('ip a | grep '.$this->prov_ip.' | tr " " "\n" | tail -n1', $prov_if);
        $this->prov_if = (isset($prov_if[0]) ? $prov_if[0] : 'eth');

        $this->domain = $conf->domain_name;
        $this->router_ip = env('NETGW_DEFAULT_GW', '172.20.3.254');
        $this->netmask = env('NETGW_IP_NETMASK', '255.255.252.0');
        $this->prefix = env('NETGW_IP_PREFIX', '22');
        $this->tf_net_1 = env('NETGW_TRANSFER_NET', '172.20.0.0'); // servers with /24
        $this->nat_ip = env('NETGW_NAT_IP', '172.20.0.2'); // second server ip is mostlikely NAT
        $this->mgmt_vlan = env('MGMT_VLAN', '100');
        $this->customer_vlan = env('CUSTOMER_VLAN', '101');

        $this->snmp_ro = $this->community_ro ?: $conf->ro_community;
        $this->snmp_rw = $this->community_rw ?: $conf->rw_community;

        // Help section: onhover
        $this->enable_secret = '<span title="NETGW_ENABLE_SECRET and NETGW_SAVE_ENCRYPTED_PASSWORDS"><b>'.$this->enable_secret.'</b></span>';
        $this->admin_psw = '<span title="NETGW_ADMIN_PASSWORD and NETGW_SAVE_ENCRYPTED_PASSWORDS"><b>'.$this->admin_psw.'</b></span>';
        $this->vty_psw = '<span title="NETGW_VTY_PASSWORD"><b>'.$this->vty_psw.'</b></span>';
        $this->prov_ip = '<span title="Set in Global Config Page / Provisioning / Provisioning Server IP"><b>'.$this->prov_ip.'</b></span>';
        $this->interface = '<span title="Depending on NETGW Device Company and Series"><b>'.$this->interface.'</b></span>';
        $this->domain = '<span title="Set in Global Config Page / Provisioning / Domain Name"><b>'.$this->domain.'</b></span>';
        $this->router_ip = '<span title="NETGW_DEFAULT_GW"><b>'.$this->router_ip.'</b></span>';
        $this->netmask = '<span title="NETGW_IP_NETMASK"><b>'.$this->netmask.'</b></span>';
        $this->prefix = '<span title="NETGW_IP_PREFIX"><b>'.$this->prefix.'</b></span>';
        $this->tf_net_1 = '<span title="NETGW_TRANSFER_NET"><b>'.$this->tf_net_1.'</b></span>';
        $this->nat_ip = '<span title="NETGW_NAT_IP"><b>'.$this->nat_ip.'</b></span>';
        $this->mgmt_vlan = '<span title="MGMT_VLAN"><b>'.$this->mgmt_vlan.'</b></span>';
        $this->customer_vlan = '<span title="CUSTOMER_VLAN"><b>'.$this->customer_vlan.'</b></span>';
        $this->snmp_ro = '<span title="Set in NETGW page or Global Config Page / Provisioning if empty in NETGW page"><b>'.$this->snmp_ro.'</b></span>';
        $this->snmp_rw = '<span title="Set in NETGW page or Global Config Page / Provisioning if empty in NETGW page"><b>'.$this->snmp_rw.'</b></span>';
    }

    /*
     * NETGW Config Page:
     * Prepare NetGw Config Variables
     *
     * They are required in NetGwBlade's
     *
     * NOTE: this will fit 90% of generic installations
     */
    public function prep_rfcard_page()
    {
        $netelement = $this->netelement;

        $clusters = [];
        if ($netelement) {
            foreach (\Modules\HfcReq\Entities\NetElement::where('netgw_id', $netelement->id)->get() as $ne) {
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
        $fn = self::US_SNR_PATH."/$this->id.json";

        if (! \Storage::exists($fn)) {
            \Log::error("Missing Modem US SNR json file of CMTS $this->hostname [$this->id]");

            return;
        }

        $snrs = json_decode(\Storage::get($fn), true);

        if (array_key_exists($ip, $snrs)) {
            return $snrs[$ip];
        }

        // L2 CMTSes may share the same IP pools
        $outdated = now()->subMinutes(10)->timestamp;
        foreach (\Storage::files(self::US_SNR_PATH) as $file) {
            // ignore files older than 10 minutes, e.g. from a decommissioned cmts
            if (\Storage::lastModified($file) > $outdated &&
                ($snrs = json_decode(\Storage::get($file), true)) !== null &&
                array_key_exists($ip, $snrs)) {
                return $snrs[$ip];
            }
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

        \Storage::put(self::US_SNR_PATH."/$this->id.json", json_encode($ret));
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
        $file = self::NETGW_INCLUDE_PATH."/$this->id.conf";

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
        $path = self::NETGW_INCLUDE_PATH;
        $incs = '';
        foreach (self::where('type', 'cmts')->get() as $cmts) {
            $incs .= "include \"$path/$cmts->id.conf\";\n";
        }
        file_put_contents($path.'.conf', $incs);
    }

    /**
     * Run vendor/series dependent OLT script to get yet unconfigured ONTs online,
     * such that we can establish a PPPoE session with them
     *
     * @author Ole Ernst
     */
    public function runSshAutoProv()
    {
        if ($this->type != 'olt' ||
            ! $this->ssh_auto_prov ||
            ! $this->username ||
            ! $this->password ||
            ! $this->ip) {
            return;
        }

        $path = \Module::getModulePath('ProvBase/Console/scripts/olt/');
        $script = $path.\Str::lower("{$this->company}_{$this->series}.sh");

        if (! file_exists($script)) {
            return;
        }

        $vlan = env('CUSTOMER_VLAN', '101');

        // run script in background since this function is called from Kernel.php
        exec("bash \"$script\" \"$this->ip\" \"$this->username\" \"$this->password\" \"$vlan\" > /dev/null &");
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
class NetGwObserver
{
    protected const NETGW_TFTP_PATH = '/tftpboot/cmts';

    public function created($netgw)
    {
        self::updateNas($netgw);

        if ($netgw->type != 'cmts') {
            return;
        }

        if (\Module::collections()->has('ProvMon')) {
            \Artisan::call('nms:cacti', ['--modem-id' => 0, '--netgw-id' => $netgw->id]);
        }
        $netgw->make_dhcp_conf();

        File::put(self::NETGW_TFTP_PATH."/$netgw->id.cfg", $netgw->get_raw_netgw_config());
    }

    public function updated($netgw)
    {
        self::updateNas($netgw);

        if ($netgw->type != 'cmts') {
            return;
        }

        $netgw->make_dhcp_conf();

        File::put(self::NETGW_TFTP_PATH."/$netgw->id.cfg", $netgw->get_raw_netgw_config());
    }

    public function deleted($netgw)
    {
        self::updateNas($netgw);

        if ($netgw->type != 'cmts') {
            return;
        }

        File::delete(NetGw::NETGW_INCLUDE_PATH."/$netgw->id.conf");
        File::delete(self::NETGW_TFTP_PATH."/$netgw->id.cfg");

        NetGw::make_includes();
    }

    /**
     * Handle changes of nas based on netgw
     * This is called on created/updated/deleted in NetGw observer
     *
     * @author Ole Ernst
     */
    private static function updateNas($netgw)
    {
        // netgw is deleted or its type was changed to != bras
        if ($netgw->deleted_at || $netgw->type != 'bras') {
            $netgw->nas()->delete();
            exec('sudo systemctl restart radiusd.service');

            return;
        }

        // we need to use \Request::get() since nas_secret is guarded
        $update = ['nasname' => $netgw->ip, 'secret' => \Request::get('nas_secret')];

        if ($netgw->nas()->count()) {
            $netgw->nas()->update($update);
        } else {
            Nas::insert(array_merge($update, ['shortname' => $netgw->id]));
        }

        exec('sudo systemctl restart radiusd.service');
    }
}
