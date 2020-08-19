<?php

namespace Modules\ProvBase\Entities;

use DB;

class IpPool extends \BaseModel
{
    // The associated SQL table for this Model
    public $table = 'ippool';

    // Add your validation rules here
    public function rules()
    {
        // Check out ExtendedValidator.php for own validations! (ip_larger, netmask)
        // Note: ip rule is added in IpPoolController
        // TODO: Take care of IpPoolController::prepare_rules() when adding new rules!
        return [
            'net' => 'required',
            'netmask' => 'required|netmask',     // netmask must not be in first place!
            'ip_pool_start' => 'required|ip_in_range:net,netmask|ip_larger:net',
            'ip_pool_end' => 'required|ip_in_range:net,netmask|ip_larger:ip_pool_start',
            'router_ip' => 'required|ip_in_range:net,netmask',
            'broadcast_ip' => 'nullable|ip_in_range:net,netmask|ip_larger:ip_pool_end',
            'dns1_ip' => 'nullable',
            'dns2_ip' => 'nullable',
            'dns3_ip' => 'nullable',
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
            'header' =>  $this->type.': '.$this->net.' '.$this->netmask,
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

    /**
     * Check if netmask is written in Cidr notation (e.g. /16)
     *
     * @param string
     * @return bool
     */
    public static function isCidrNotation($netmask)
    {
        return preg_match('/^\/\d{1,3}$/', $netmask);
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
        $empty = "\t\t\t#pool: $this->type $this->ip_pool_start $this->ip_pool_end\n\t\t\trange $this->ip_pool_start $this->ip_pool_end;\n";

        if ($this->type != 'CPEPub') {
            return $empty;
        }

        // TODO: filter endpoints by DB query with INET_ATON
        $endpoints = Endpoint::where('fixed_ip', '=', '1')->get();

        if ($endpoints->count() == 0) {
            return $empty;
        }

        foreach ($endpoints as $ep) {
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
        self::updateRadIpPool($pool);

        if ($pool->netgw->type != 'cmts') {
            return;
        }

        // fetch netgw object that is related to the created ippool and make dhcp conf
        $pool->netgw->make_dhcp_conf();
    }

    public function updated($pool)
    {
        self::updateRadIpPool($pool);

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
        self::updateRadIpPool($pool);

        if ($pool->netgw->type != 'cmts') {
            return;
        }

        $pool->netgw->make_dhcp_conf();
    }

    /**
     * Handle changes of radippool based on ippool
     * This is called on created/updated/deleted in IpPool observer
     *
     * @author Ole Ernst
     */
    private static function updateRadIpPool($pool)
    {
        \Queue::push(new \Modules\ProvBase\Jobs\RadIpPoolJob($pool, $pool->getDirty(), $pool->getOriginal(), $pool->wasRecentlyCreated));
    }
}
