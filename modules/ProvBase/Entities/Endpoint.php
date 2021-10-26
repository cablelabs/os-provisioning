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

use Request;

class Endpoint extends \BaseModel
{
    // The associated SQL table for this Model
    public $table = 'endpoint';

    /**
     * This ruleset is currently very complex and extended in EndpointController.php - please checkout the
     * documentation under https://devel.roetzer-engineering.com/confluence/display/LAR/Varieties+of+creating+endpoints
     * if you want to change anything here
     *
     * @author Nino Ryschawy
     *
     * @return array
     */
    public function rules()
    {
        $id = $this->id ?: 0;
        $modem = $this->exists ? $this->modem : Modem::with('configfile')->find(Request::get('modem_id'));

        // Hostname/MAC must be unique only inside all ipv4 or ipv6 endpoints - on creation it must be compared to version=NULL to work
        $versionFilter = ',version,'.($this->version ?: 'NULL');

        $rules = [
            'mac' => ['nullable', 'mac', 'unique:endpoint,mac,'.$id.',id,deleted_at,NULL'.$versionFilter],
            'hostname' => ['required', 'regex:/^(?!cm-)(?!mta-)[0-9A-Za-z\-]+$/',
                'unique:endpoint,hostname,'.$id.',id,deleted_at,NULL'.$versionFilter, ],
            'ip' => ['nullable', 'required_if:fixed_ip,1', 'ip', 'unique:endpoint,ip,'.$id.',id,deleted_at,NULL'],
        ];

        if ($modem) {
            if ($modem->configfile->device == 'cm') {
                // Note: For IPv4 this is removed in EndpointController.php
                $rules['mac'][] = 'required';
            } else {
                $rules['fixed_ip'][] = 'In:1';
                $rules['ip'][] = 'required';
                array_unshift($rules['ip'], 'ipv4');
            }
        }

        return $rules;
    }

    // Name of View
    public static function view_headline()
    {
        return 'Endpoint';
    }

    // View Icon
    public static function view_icon()
    {
        return '<i class="fa fa-map-marker"></i>';
    }

    // AJAX Index list function
    // generates datatable content and classes for model
    public function view_index_label()
    {
        $bsclass = $this->get_bsclass();

        return ['table' => $this->table,
            'index_header' => [$this->table.'.hostname', $this->table.'.mac', $this->table.'.ip', $this->table.'.description'],
            'header' =>  $this->label(),
            'bsclass' => $bsclass, ];
    }

    public function get_bsclass()
    {
        $bsclass = 'success';

        return $bsclass;
    }

    public function label()
    {
        $label = $this->hostname.' ';
        $labelExt = [];

        if ($this->mac) {
            $labelExt[] = $this->mac;
        }

        if ($this->fixed_ip && $this->ip) {
            $labelExt[] = $this->ip;
        }

        $label .= $labelExt ? '('.implode(' / ', $labelExt).')' : '';

        return $label;
    }

    public function view_belongs_to()
    {
        return $this->modem;
    }

    /**
     * all Relations:
     */
    public function modem()
    {
        return $this->belongsTo(Modem::class);
    }

    /**
     * @return obj|null \Modules\ProvBase\Entities\NetGw
     */
    public function netGw()
    {
        if (! $this->ip) {
            return;
        }

        $query = NetGw::join('ippool as i', 'netgw.id', 'i.netgw_id')
            ->where('i.type', 'CPEPub')
            ->where('i.ip_pool_start', '<=', $this->ip)
            ->where('i.ip_pool_end', '>=', $this->ip)
            ->select('netgw.*', 'i.net', 'i.ip_pool_start', 'i.ip_pool_end');

        return $query->first();

        // return new \Illuminate\Database\Eloquent\Relations\BelongsTo($query, new NetGw, null, 'deleted_at', 'netGw');
    }

    /**
     * BOOT:
     * - init modem observer
     */
    public static function boot()
    {
        parent::boot();

        self::observe(new \Modules\ProvBase\Observers\EndpointObserver);
        self::observe(new \App\Observers\SystemdObserver);
    }

    public function makeDhcp()
    {
        if ($this->version == '4') {
            self::makeDhcp4All();
        }

        if ($this->version == '6') {
            self::makeDhcp6All();
        }
    }

    /**
     * Make DHCP config for EPs
     */
    public static function makeDhcp4All()
    {
        $dir = '/etc/dhcp-nmsprime/';
        $file_ep = $dir.'endpoints-host.conf';

        $data = '';
        $endpoints = self::where('version', '4')->join('modem as m', 'm.id', 'endpoint.modem_id')
            ->select('endpoint.*', 'm.mac as modemmac')
            ->get();

        foreach ($endpoints as $ep) {
            $data .= "host $ep->hostname { ";
            $data .= $ep->mac ? "hardware ethernet $ep->mac" : "host-identifier option agent.remote-id $ep->modemmac";
            $data .= ' ; ';

            if ($ep->fixed_ip && $ep->ip) {
                $data .= "fixed-address $ep->ip; ";
            }

            $data .= "}\n";
        }

        $ret = file_put_contents($file_ep, $data, LOCK_EX);
        if ($ret === false) {
            exit('Error writing to file');
        }

        // chown for future writes in case this function was called from CLI via php artisan nms:dhcp that changes owner to 'root'
        system('/bin/chown -R apache /etc/dhcp-nmsprime/');

        return $ret > 0;
    }

    public static function makeDhcp6All()
    {
        $file = '/etc/kea/hosts6.conf';

        $hosts = self::where('version', '6')->whereNotNull('mac')->whereNotNull('ip')->get();

        $reservations = [];
        foreach ($hosts as $host) {
            $reservation = "\n{ \"hw-address\": \"".$host->mac.'",';
            $reservation .= ' "ip-addresses": [ "'.$host->ip.'" ],';
            $reservation .= ' "prefixes": [ "'.$host->prefix.'" ],';
            $reservation .= ' "hostname": "'.$host->hostname.'" }';

            $reservations[] = $reservation;
        }

        $data = '"reservations": ['.implode(',', $reservations)."\n]";

        file_put_contents($file, $data, LOCK_EX);
    }

    public function makeNetGwConf()
    {
        $ngw = $this->netGw();

        if (! $ngw) {
            if ($this->modem->configfile->device == 'cm') {
                session()->push('tmp_error_above_form', trans('messages.endpoint.noNgw', ['ip' => $this->ip]));
            }

            return;
        }

        $ngw->makeDhcp4Conf();
    }

    public function nsupdate($del = false)
    {
        $provbase = ProvBase::first();

        if ($this->version == '4') {
            $cmd = $this->getNsupdate4Cmd($del);
        } else {
            $cmd = $this->getNsupdate6Cmd($del);
        }
        if (! $cmd) {
            return;
        }

        // detect servers to be updated
        $servers = [];
        if (! \Module::collections()->has('ProvHA')) {
            $servers['127.0.0.1'] = $provbase->dns_password;
        } else {
            $servers['127.0.0.1'] = $provbase->provhaOwnDnsPw;
            $servers[$provbase->provhaPeerIp] = $provbase->provhaPeerDnsPw;
        }

        foreach ($servers as $server => $password) {
            $server_cmd = str_replace('update ', "server $server\nupdate ", $cmd);
            $handle = popen("/usr/bin/nsupdate -v -y dhcpupdate:$password", 'w');
            fwrite($handle, $server_cmd);
            pclose($handle);

            $log = [
                '',
                '--------------------------------------------------------------------------------',
                date('c'),
                __METHOD__,
                $server_cmd,
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

    /**
     * Generate nsupdate command for IPv4
     *
     * @return string
     */
    private function getNsupdate4Cmd($del)
    {
        $cmd = '';
        $zone = ProvBase::first()->domain_name;

        if ($del) {
            if ($this->getOriginal('fixed_ip') && $this->getOriginal('ip')) {
                $rev = implode('.', array_reverse(explode('.', $this->getOriginal('ip'))));
                $cmd .= "update delete {$this->getOriginal('hostname')}.cpe.$zone. IN A\nsend\n";
                $cmd .= "update delete $rev.in-addr.arpa.\nsend\n";
            } else {
                $mangle = exec("echo '{$this->getOriginal('mac')}' | tr -cd '[:xdigit:]' | xxd -r -p | openssl dgst -sha256 -mac hmac -macopt hexkey:$(cat /etc/named-ddns-cpe.key) -binary | python -c 'import base64; import sys; print(base64.b32encode(sys.stdin.read())[:6].lower())'");
                $cmd .= "update delete {$this->getOriginal('hostname')}.cpe.$zone.\nsend\n";
                $cmd .= "update delete $mangle.cpe.$zone.\nsend\n";
            }
        } else {
            if ($this->fixed_ip && $this->ip) {
                // endpoints with a fixed-address will get an A and PTR record (ip <-> hostname)
                $rev = implode('.', array_reverse(explode('.', $this->ip)));
                $cmd .= "update add $this->hostname.cpe.$zone. 3600 A $this->ip\nsend\n";
                $cmd .= "update add $rev.in-addr.arpa. 3600 PTR $this->hostname.cpe.$zone.\nsend\n";
                if ($this->add_reverse) {
                    $cmd .= "update add $rev.in-addr.arpa. 3600 PTR $this->add_reverse.\nsend\n";
                }
            } else {
                // other endpoints will get a CNAME record (hostname -> mangle)
                // mangle name is based only on cpe mac address
                $mangle = exec("echo '$this->mac' | tr -cd '[:xdigit:]' | xxd -r -p | openssl dgst -sha256 -mac hmac -macopt hexkey:$(cat /etc/named-ddns-cpe.key) -binary | python -c 'import base64; import sys; print(base64.b32encode(sys.stdin.read())[:6].lower())'");
                $cmd .= "update add $this->hostname.cpe.$zone. 3600 CNAME $mangle.cpe.$zone.\nsend\n";
            }
        }

        return $cmd;
    }

    /**
     * Generate nsupdate command for IPv6
     *
     * @return string
     */
    private function getNsupdate6Cmd($del)
    {
        $cmd = '';
        $zone = ProvBase::first()->domain_name;

        // We currently don't add a CNAME record here because there's no automatically created mangle hostname to point to

        if ($del) {
            if ($this->getOriginal('fixed_ip') && $this->getOriginal('ip')) {
                $arpa = self::getV6Arpa($this->getOriginal('ip'));
                $cmd .= "update delete {$this->getOriginal('hostname')}.cpe.$zone. IN AAAA\nsend\n";
                $cmd .= "update delete $arpa.\nsend\n";
            }
        } else {
            if ($this->fixed_ip && $this->ip) {
                // endpoints with a fixed-address will get an A and PTR record (ip <-> hostname)
                $arpa = self::getV6Arpa($this->ip);
                $cmd .= "update add $this->hostname.cpe.$zone. 3600 AAAA $this->ip\nsend\n";
                $cmd .= "update add $arpa. 3600 PTR $this->hostname.cpe.$zone.\nsend\n";
                if ($this->add_reverse) {
                    $cmd .= "update add $arpa. 3600 PTR $this->add_reverse.\nsend\n";
                }
            }
        }

        return $cmd;
    }

    /**
     * Generate reverse notation of IPv6 for DNS server
     *
     * See https://stackoverflow.com/questions/6619682/convert-ipv6-to-nibble-format-for-ptr-records
     *
     * @return string
     */
    public static function getV6Arpa($ip)
    {
        $addr = inet_pton($ip);
        $unpack = unpack('H*hex', $addr);
        $hex = $unpack['hex'];

        return implode('.', array_reverse(str_split($hex))).'.ip6.arpa';
    }

    /**
     * Get next hostname for a new Endpoint that shall be created via GUI
     *
     * @author Nino Ryschawy
     *
     * @return string e.g. cpe-100010-2 | null when used in place where Request doesn't contain modem_id
     */
    public static function getNewHostname()
    {
        if (! Request::has('modem_id')) {
            return;
        }

        $modem = Modem::find(Request::get('modem_id'));
        $default = 'cpe-'.$modem->id;

        if ($modem->endpoints->isEmpty()) {
            return $default;
        }

        $lastHostname = $modem->endpoints->filter(function ($item) use ($default) {
            if (strpos($item->hostname, $default.'-') !== false) {
                return $item;
            }
        })->pluck('hostname')->sort()->last();

        if (! $lastHostname) {
            return $default.'-2';
        }

        return $default.'-'.(substr(strrchr($lastHostname, '-'), 1) + 1);
    }
}
