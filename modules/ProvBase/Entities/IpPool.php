<?php

namespace Modules\ProvBase\Entities;

use DB;

class IpPool extends \BaseModel
{
    // The associated SQL table for this Model
    public $table = 'ippool';

    // Add your validation rules here
    public static function rules($id = null)
    {
        return [
            'net' => 'required|ip',
            'netmask' => 'required|ip|netmask',     // netmask must not be in first place!
            'ip_pool_start' => 'required|ip|ip_in_range:net,netmask|ip_larger:net',   // own validation - see in classes: ExtendedValidator and IpPoolController
            'ip_pool_end' => 'required|ip|ip_in_range:net,netmask|ip_larger:ip_pool_start',
            'router_ip' => 'required|ip|ip_in_range:net,netmask',
            'broadcast_ip' => 'nullable|ip|ip_in_range:net,netmask|ip_larger:ip_pool_end',
            'dns1_ip' => 'nullable|ip',
            'dns2_ip' => 'nullable|ip',
            'dns3_ip' => 'nullable|ip',
        ];
    }

    // Name of View
    public static function view_headline()
    {
        return 'IP-Pools';
    }

    // View Icon
    public static function view_icon()
    {
        return '<i class="fa fa-tags"></i>';
    }

    // AJAX Index list function
    // generates datatable content and classes for model
    public function view_index_label()
    {
        $bsclass = $this->get_bsclass();

        return ['table' => $this->table,
                'index_header' => [$this->table.'.id', 'netgw.hostname', $this->table.'.type', $this->table.'.net', $this->table.'.netmask', $this->table.'.router_ip', $this->table.'.description'],
                'header' =>  $this->type.': '.$this->net.' / '.$this->netmask,
                'bsclass' => $bsclass,
                'eager_loading' => ['netgw'], ];
    }

    public function get_bsclass()
    {
        $bsclass = 'success';

        if ($this->type == 'CPEPub') {
            $bsclass = 'warning';
        }
        if ($this->type == 'CPEPriv') {
            $bsclass = 'info';
        }
        if ($this->type == 'MTA') {
            $bsclass = 'danger';
        }

        return $bsclass;
    }

    /**
     * Returns all netgw hostnames for ip pools as an array
     */
    public function netgw_hostnames()
    {
        return DB::table('netgw')->select('id', 'hostname')->get();
    }

    /*
     * Return the corresponding network size to the netmask,
     * e.g. 255.255.255.240 will return 28 as integer – means /28 netmask
     */
    public function size()
    {
        // this is crazy shit from http://php.net/manual/de/function.ip2long.php
        $long = ip2long($this->netmask);
        $base = ip2long('255.255.255.255');

        return 32 - log(($long ^ $base) + 1, 2);
    }

    /*
     * Returns true if provisioning route to $this pool exists, otherwise false
     */
    public function ip_route_prov_exists()
    {
        // route is online without setting a static route,
        // e.g. an external router is used (default gateway)
        if ($this->ip_route_online()) {
            return true;
        }

        return strlen(exec('/usr/sbin/ip route show '.$this->net.'/'.$this->size().' via '.$this->netgw->ip)) == 0 ? false : true;
    }

    /*
     * Return true if $this->router_ip is online, otherwise false
     * This implies that the NETGW pool should be set correctly in the NETGW
     */
    public function ip_route_online()
    {
        // Ping: Only check if device is online
        exec('sudo ping -c1 -i0 -w1 '.$this->router_ip, $ping, $ret);

        return $ret ? false : true;
    }

    /**
     * Return the cisco wildcard mask, which is the inverted subnet mask
     *
     * @return string
     *
     * @author Ole Ernst
     */
    public function wildcard_mask()
    {
        foreach (explode('.', $this->netmask) as $val) {
            $mask[] = ~intval($val) & 255;
        }

        return implode('.', $mask);
    }

    /**
     * Return 'secondary' if this pool is not the first CM pool of the NETGW,
     * otherwise an empty string
     *
     * @return string
     *
     * @author Ole Ernst
     */
    public function is_secondary()
    {
        $cm_pools = $this->netgw->ippools->filter(function ($item) {
            return $item->type == 'CM';
        });

        if ($cm_pools->isEmpty() || $this->id != $cm_pools->first()->id) {
            return 'secondary';
        }

        return '';
    }

    /**
     * Return the range string according to the IpPool. We need to cut out public
     * CPE IP addresses, which have been statically assigned - so that they won't
     * be given out to multiple CPEs
     *
     * @return string
     *
     * @author Ole Ernst
     */
    public function get_range()
    {
        $ep_static = Endpoint::where('fixed_ip', '=', '1');

        if ($this->type != 'CPEPub' || $ep_static->count() == 0) {
            return "\t\t\t#pool: $this->type $this->ip_pool_start $this->ip_pool_end\n\t\t\trange $this->ip_pool_start $this->ip_pool_end;\n";
        }

        foreach ($ep_static->get() as $ep) {
            $static[] = ip2long($ep->ip);
        }

        $leases = array_diff(range(ip2long($this->ip_pool_start), ip2long($this->ip_pool_end)), $static);
        if (! $leases) {
            return;
        }

        $start = long2ip(reset($leases));
        $end = long2ip(end($leases));

        $pool = "\t\t\t#pool: $this->type $start $end\n";
        foreach ($leases as $lease) {
            $pool .= "\t\t\trange ".long2ip($lease).";\n";
        }

        return $pool;
    }

    /**
     * Returns the next unused IP address of all BRAS NetGWs, if none is found
     * false is returned
     *
     * We don't look at a specific BRAS, since the IP addresses can be assigned
     * to any BRAS using RADIUS attributes (Framed-IP-Address) and will be
     * announced via OSPF as a /32 route
     *
     * @return mixed
     *
     * @author Ole Ernst
     */
    public static function findNextUnusedBrasIPAddress($public)
    {
        $type = $public ? 'CPEpub' : 'CPEpriv';

        $pools = \DB::table('netgw')
            ->join('ippool', 'netgw.id', '=', 'ippool.netgw_id')
            ->where('netgw.type', 'bras')
            ->where('ippool.type', $type)
            ->whereNull('netgw.deleted_at')
            ->whereNull('ippool.deleted_at')
            ->get();

        $all = [];
        foreach ($pools as $pool) {
            $all = array_merge($all, range(ip2long($pool->ip_pool_start), ip2long($pool->ip_pool_end)));
        }
        sort($all);

        /*
        // for a larger number of hosts this is probably slow,
        // since we gethostbyname() for every individual host
        $used = \DB::table('configfile')
            ->join('modem', 'configfile.id', '=', 'modem.configfile_id')
            ->where('configfile.device', 'tr069')
            ->whereNull('configfile.deleted_at')
            ->whereNull('modem.deleted_at')
            ->pluck('modem.hostname')
            ->transform(function ($item, $key) {
                return ip2long(gethostbyname($item));
            })->toArray();
        */

        // a zone transfer should be quicker
        $zone = ProvBase::first()->domain_name;
        $used = shell_exec("dig -tAXFR $zone | grep '^ppp-[[:digit:]]\+' | awk '{print $5}' | sort -u");
        $used = array_map('ip2long', explode("\n", trim($used)));
        sort($used);

        $free = array_diff($all, $used);

        if (empty($free)) {
            return false;
        }

        return long2ip(reset($free));
    }

    /**
     * Relationships:
     */
    public function netgw()
    {
        return $this->belongsTo(NetGw::class);
    }

    // belongs to a netgw - see BaseModel for explanation
    public function view_belongs_to()
    {
        return $this->netgw;
    }

    /**
     * BOOT:
     * - init ippool observer
     */
    public static function boot()
    {
        parent::boot();

        self::observe(new IpPoolObserver);
        self::observe(new \App\SystemdObserver);
    }
}

/**
 * IP-Pool Observer Class
 * Handles changes on IP-Pools
 *
 * can handle   'creating', 'created', 'updating', 'updated',
 *              'deleting', 'deleted', 'saving', 'saved',
 *              'restoring', 'restored',
 */
class IpPoolObserver
{
    public function created($pool)
    {
        if ($pool->netgw->type != 'cmts') {
            return;
        }

        // fetch netgw object that is related to the created ippool and make dhcp conf
        $pool->netgw->make_dhcp_conf();
    }

    public function updated($pool)
    {
        if ($pool->netgw->type != 'cmts') {
            return;
        }

        $pool->netgw->make_dhcp_conf();

        // make dhcp conf of old netgw if relation got changed
        if ($pool->isDirty('netgw_id')) {
            NetGw::find($pool->getOriginal('netgw_id'))->make_dhcp_conf();
        }
    }

    public function deleted($pool)
    {
        if ($pool->netgw->type != 'cmts') {
            return;
        }

        $pool->netgw->make_dhcp_conf();
    }
}
