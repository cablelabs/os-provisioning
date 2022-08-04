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

namespace Modules\ProvBase\Entities;

use File;
use App\Sla;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class NetGw extends \BaseModel
{
    public const TYPES = ['cmts', 'bras', 'olt', 'dslam'];
    // don't put a trailing slash here!
    public const NETGW_INCLUDE_PATH = '/etc/dhcp-nmsprime/cmts_gws';
    public const US_SNR_PATH = 'data/provmon/us_snr';
    public const US_OFDMA_PATH = 'data/provmon/ofdma';
    protected const DHCP6_GATEWAYS_FILE = '/etc/kea/gateways6.conf';
    protected const DHCP6_GATEWAYS_DIR = '/etc/kea/gateways6';

    // The associated SQL table for this Model
    public $table = 'netgw';

    // Attributes
    public $guarded = ['formatted_support_state'];
    protected $appends = ['formatted_support_state'];
    protected $with = ['ippools', 'netelement:id,cluster,net,ip,parent_id,netelementtype_id,prov_device_id,_lft,_rgt'];
    protected $without = ['netelement.netelementtype'];

    // Add your validation rules here
    public function rules()
    {
        $types = implode(',', self::TYPES);

        return [
            'hostname' => 'required|unique:netgw,hostname,'.($this->id ?: 0).',id,deleted_at,NULL',  	// unique: table, column, exception , (where clause)
            'company' => 'required',
            'type' => "required|in:$types",
            'coa_port' => 'nullable|numeric|min:1|max:65535',
            'ssh_port' => 'nullable|numeric|min:1|max:65535',
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

    // AJAX Index list function
    // generates datatable content and classes for model
    public function view_index_label()
    {
        $ret = ['table' => $this->table,
            'index_header' => [$this->table.'.id', $this->table.'.hostname', 'type', $this->table.'.ip', $this->table.'.company', $this->table.'.series'],
            'header' =>  $this->label(),
            'bsclass' => $this->get_bsclass(),
        ];

        if (false && Sla::firstCached()->valid()) {
            $ret['index_header'][] = $this->table.'.support_state';
            $ret['edit']['support_state'] = 'getSupportState';
            $ret['raw_columns'][] = 'support_state';
        }

        return $ret;
    }

    public function get_bsclass()
    {
        // TODO: use netgw state value
        if ($this->state == 1) {
            return 'warning';
        }

        if ($this->state == 2) {
            return 'danger';
        }

        return 'success';
    }

    public function label()
    {
        return $this->hostname;
    }

    /**
     * Return Fontawesome emoji class, and Bootstrap text color
     *
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
     *
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

        self::observe(new \Modules\ProvBase\Observers\NetGwObserver);
        self::observe(new \App\Observers\SystemdObserver);
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

        if ($this->netelement) {
            $ret['NetGw'] = $ret['Edit'];
            unset($ret['Edit']);
        }

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
        $this->provBase = ProvBase::first();
        $this->prov_ip = $this->provBase->provisioning_server;
        exec('ip a | grep '.$this->prov_ip.' | tr " " "\n" | tail -n1', $prov_if);
        $this->prov_if = (isset($prov_if[0]) ? $prov_if[0] : 'eth');

        $this->domain = $this->provBase->domain_name;
        $this->router_ip = env('NETGW_DEFAULT_GW', '172.20.3.254');
        $this->netmask = env('NETGW_IP_NETMASK', '255.255.252.0');
        $this->prefix = env('NETGW_IP_PREFIX', '22');
        $this->tf_net_1 = env('NETGW_TRANSFER_NET', '172.20.0.0'); // servers with /24
        $this->nat_ip = env('NETGW_NAT_IP', '172.20.0.2'); // second server ip is mostlikely NAT
        $this->mgmt_vlan = env('MGMT_VLAN', '100');
        $this->customer_vlan = env('CUSTOMER_VLAN', '101');
        $this->netmask6 = env('NETGW_IP6_NETMASK', '/116');

        $this->snmp_ro = $this->community_ro ?: $this->provBase->ro_community;
        $this->snmp_rw = $this->community_rw ?: $this->provBase->rw_community;

        // add data used if ProvHA is enabled
        if (\Module::collections()->has('ProvHA')) {
            $provha = \DB::table('provha')->first();
            $this->provha_servers = [$provha->master];
            $this->provha_servers = array_merge($this->provha_servers, explode(',', $provha->slaves));
        }

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
        $clusters = [];
        if (\Module::collections()->has('HfcReq') && $netelement = $this->netelement) {
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
    public function getUsSnr($ip)
    {
        $fn = self::US_SNR_PATH."/$this->id.json";

        if (! Storage::exists($fn)) {
            \Log::error("Missing Modem US SNR json file of CMTS $this->hostname [$this->id]");

            return;
        }

        $snrs = json_decode(Storage::get($fn), true);

        if (array_key_exists($ip, $snrs)) {
            return $snrs[$ip];
        }

        // L2 CMTSes may share the same IP pools
        $outdated = now()->subMinutes(30)->timestamp;
        foreach (Storage::files(self::US_SNR_PATH) as $file) {
            // ignore files older than 10 minutes, e.g. from a decommissioned cmts
            if (Storage::lastModified($file) > $outdated &&
                ($snrs = json_decode(Storage::get($file), true)) !== null &&
                array_key_exists($ip, $snrs)) {
                return $snrs[$ip];
            }
        }
    }

    /**
     * Store US SNR/OFDMA values for all modems once every 5 minutes
     * this greatly reduces the cpu load on the cmts
     *
     * @author Ole Ernst
     * @author Nino Ryschawy  - D2.0 Extension
     */
    public function storeUsValues()
    {
        $ret['SNR'] = [];
        $freqs = [];
        $ips = [];
        $snrs = [];
        $d2ChIdxs = [];
        $d2Snrs = [];

        $ret['OFDMA'] = $this->mapOfdmaChannelDataToMac();
        Storage::put(self::US_OFDMA_PATH."/$this->id.json", json_encode($ret['OFDMA']));

        $fn = self::US_SNR_PATH."/{$this->id}.php";
        if (! Storage::exists($fn)) {
            return;
        }

        require_once storage_path("app/$fn");
        Storage::delete($fn);

        $freqs = array_map(function ($freq) {
            return strval($freq / 1000000);
        }, $freqs);

        $ips = array_map(function ($hex) {
            return long2ip(hexdec(preg_replace('/[^[:xdigit:]]/', '', $hex)));
        }, $ips);

        foreach ($ips as $ipIdx => $ip) {
            if ($ip == '0.0.0.0') {
                continue;
            }

            foreach ($snrs as $snrOid => $snr) {
                [$snrIpIdx, $snrFreqIdx] = explode('.', $snrOid);

                if ($snrIpIdx != $ipIdx) {
                    continue;
                }

                try {
                    $ret['SNR'][$ip][$freqs[$snrFreqIdx]] = $snr / 10;
                } catch (\ErrorException $e) {
                }
            }

            // fallback to D2.0 to retrive at least one US SNR value
            if (empty($ret['SNR'][$ip]) && isset($d2ChIdxs[$ipIdx]) && isset($freqs[$d2ChIdxs[$ipIdx]]) && isset($d2Snrs[$ipIdx])) {
                $ret['SNR'][$ip][$freqs[$d2ChIdxs[$ipIdx]]] = $d2Snrs[$ipIdx] / 10;
            }
        }

        Storage::put(self::US_SNR_PATH."/$this->id.json", json_encode($ret['SNR']));
    }

    public function mapOfdmaChannelDataToMac()
    {
        $iucStats = [];
        $macs = [];
        $iucList = [];
        $fn = self::US_OFDMA_PATH."/{$this->id}.php";

        if (! Storage::exists($fn)) {
            return [];
        }

        require_once storage_path("app/$fn");
        Storage::delete($fn);

        $iucList = Arr::where($iucList, function ($value, $key) {
            return $value !== '';
        });

        /* first 4 octets: ifIndex
         * next 2 octets: number or count of Data IUCs
         * next octet: Data IUC (5, 6, 9-13)
         */
        $data = array_map(function ($hex) {
            // split string in chunks of length 14 since there can be multiple OFDMA Channels
            foreach (str_split(str_replace(' ', '', $hex), 14) as $key => $value) {
                return [$key => [hexdec(substr($value, 0, 8)), hexdec(substr($value, 8, 4)), hexdec(substr($value, 12, 2))]];
            }
        }, $iucList);

        $ret = [];
        foreach ($macs as $ifIndex => $mac) {
            $ret['iucList'][$mac] = $data[$ifIndex] ?? null;
        }

        if ($iucStats) {
            $ret['iucStats'] = $iucStats;
        }

        return $ret;
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

    public function makeDhcpConf()
    {
        $this->makeDhcp4Conf();
        $this->makeDhcp6Conf();
    }

    /**
     * auto generates the dhcp conf file for a specified netgw and
     * adds the appropriate include statement in cmts_gws.conf
     *
     * (description is automatically taken by phpdoc)
     *
     * TODO: improve performance by collecting data first and put to file once at the end
     *
     * @author Nino Ryschawy
     */
    public function makeDhcp4Conf()
    {
        $file = self::NETGW_INCLUDE_PATH."/$this->id.conf";

        $ippools = $this->ippools->where('version', '4');

        // if a cmts doesn't have an ippool the file has to be empty
        if ($ippools->isEmpty()) {
            File::put($file, '');
            goto _exit;
        }

        File::put($file, 'shared-network "'.$this->hostname.'"'."\n".'{'."\n");

        foreach ($ippools as $pool) {
            $active = $pool->active;
            $subnet = strstr($pool->net, '/', true);
            $broadcast_addr = $pool->broadcast_ip;
            $ranges = $pool->getRanges();
            $dns['1'] = $pool->dns1_ip;
            $dns['2'] = $pool->dns2_ip;
            $dns['3'] = $pool->dns3_ip;

            $data = "\n\t".'subnet '.$subnet.' netmask '.$pool->netmask."\n\t".'{';
            $data .= "\n\t\t".'option routers '.$pool->router_ip.';';
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

            if ($ranges) {
                $data .= "\n\n\t\t".'pool'."\n\t\t{\n";
                $data .= $ranges;
                if (\Module::collections()->has('ProvHA')) {
                    $data .= "\n\t\t\t".'failover peer "dhcpd-failover";'."\n";
                }

                if ($active) {
                    switch ($pool->type) {
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

                        case 'STB':
                            $data .= "\n\t\t\t".'allow members of "STB";';
                            break;

                        default:
                            // code...
                            break;
                    }
                } else {
                    $data .= "\n\t\t\t".'deny all clients;';
                }

                $data .= "\n\t\t".'}';
            }

            // append additional options
            if ($pool->optional) {
                $data .= "\n\n\t\t".$pool->optional;
            }

            $data .= "\n\t".'}'."\n";

            $data .= "\n\tsubnet $pool->router_ip netmask 255.255.255.255\n\t{";
            $data .= "\n\t\tallow leasequery;";
            $data .= "\n\t}\n";

            File::append($file, $data);
        }

        File::append($file, '}'."\n");

        _exit:
        self::make_includes();
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
     * Generates the Kea DHCP configuration file for a NetGw
     *
     * @author Nino Ryschawy
     */
    public function makeDhcp6Conf()
    {
        \Log::debug('Make DHCP6 conf');

        $file = self::DHCP6_GATEWAYS_DIR."/$this->id.conf";

        if ($this->trashed()) {
            File::delete($file);
            self::makeIncludesV6();

            return;
        }

        $data = "{\n\t".'"name": "'.$this->id.'"';

        $pools = $this->ippools->where('version', '6');
        $subnets = [];

        foreach ($pools as $pool) {
            $subnet = "\t\t{\n\t\t\t".'"subnet": "'.$pool->net.'",';
            $subnet .= "\n\t\t\t".'"pools": [ { "pool": "'.$pool->ip_pool_start.'-'.$pool->ip_pool_end.'" } ]';

            // Note: Source address of relay forward message (solicit, request) must either be inside the range of the subnet
            // or specified as relay (global or in a subnet) to be able to select an address from an appropriate subnet
            if ($pool->router_ip) {
                $subnet .= ",\n\t\t\t".'"relay": { "ip-addresses": [ "'.$pool->router_ip.'" ] }';
            }

            $subnet .= ",\n\t\t\t".'"pd-pools" : [{'."\n\t\t\t\t".'"prefix": "'.$pool->prefix.'"';
            $subnet .= ",\n\t\t\t\t".'"prefix-len": '.str_replace('/', '', $pool->prefix_len);
            $subnet .= ",\n\t\t\t\t".'"delegated-len": '.str_replace('/', '', $pool->delegated_len)."\n\t\t\t}]";

            // Add DNS servers
            $dnsServers = [];
            for ($i = 1; $i <= 3; $i++) {
                if ($pool->{'dns'.$i.'_ip'}) {
                    $dnsServers[] = $pool->{'dns'.$i.'_ip'};
                }
            }

            if ($dnsServers) {
                $subnet .= ",\n\t\t\t\"option-data\": [{";
                $subnet .= "\n\t\t\t\t\"name\": \"dns-servers\"";
                $subnet .= ",\n\t\t\t\t".'"data": "'.implode(', ', $dnsServers)."\"\n\t\t\t";
                $subnet .= "\n\t\t\t}]";
            }

            if ($pool->optional) {
                $data .= ",\n\t\t\t".$pool->optional;
            }

            // Make host reservations known to all subnets
            $subnet .= ",\n\t\t\t".'<?include "/etc/kea/hosts6.conf"?>';

            $subnets[] = $subnet."\n\t\t}";
        }

        if ($subnets) {
            $data .= ",\n\t\"subnet6\": [\n".implode(",\n", $subnets)."\n\t]";
        }

        // end of shared network
        $data .= "\n}";

        File::put($file, $data, true);

        self::makeIncludesV6();
    }

    /**
     * Generates gateways.conf containing includes for the shared-networks of all NetGws for Kea DHCP
     *
     * @author Nino Ryschawy
     */
    public static function makeIncludesV6()
    {
        $incs = [];
        foreach (self::where('type', 'cmts')->get() as $cmts) {
            $incs[] = '<?include "'.self::DHCP6_GATEWAYS_DIR."/$cmts->id.conf\"?>";
        }

        File::put(self::DHCP6_GATEWAYS_FILE, implode(",\n", $incs), true);
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

        $port = $this->ssh_port ?? 22;
        $vlan = env('CUSTOMER_VLAN', '101');

        // run script in background since this function is called from Kernel.php
        exec("bash \"$script\" \"$this->ip\" \"$this->username\" \"$this->password\" \"$port\" \"$vlan\" > /dev/null &");
    }
}
