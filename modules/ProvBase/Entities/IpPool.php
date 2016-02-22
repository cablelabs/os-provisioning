<?php

namespace Modules\ProvBase\Entities;

use DB;
use Modules\ProvBase\Entities\Cmts;

class IpPool extends \BaseModel {

    // The associated SQL table for this Model
    public $table = 'ippool';

    // Add your validation rules here
    public static function rules($id = null)
    {
        return array(
            'net' => 'required|ip',
            'netmask' => 'required|ip|netmask',     // netmask must not be in first place!
            'ip_pool_start' => 'required|ip|ip_in_range:net,netmask|ip_larger:net',   // own validation - see in classes: ExtendedValidator and IpPoolController
            'ip_pool_end' => 'required|ip|ip_in_range:net,netmask|ip_larger:ip_pool_start',
            'router_ip' => 'required|ip|ip_in_range:net,netmask|ip_larger:ip_pool_start',
            'broadcast_ip' => 'ip|ip_in_range:net,netmask|ip_larger:ip_pool_start',
            'dns1_ip' => 'ip',
            'dns2_ip' => 'ip',
            'dns3_ip' => 'ip'
        );
    }


    // Name of View
    public static function get_view_header()
    {
        return 'IP-Pools';
    }

    // link title in index view
    public function get_view_link_title()
    {
        // return $this->net.' - '.$this->netmask;
        return $this->html_list($this->cmts_hostnames(), 'hostname')[$this->cmts_id].'-'.$this->id;
    }


    /**
     * Returns all cmts hostnames for ip pools as an array
     */
    public function cmts_hostnames ()
    {
        return DB::table('cmts')->select('id', 'hostname')->get();
    }


    /**
     * Relationships:
     */

    public function cmts()
    {
        return $this->belongsTo('Modules\ProvBase\Entities\Cmts', 'cmts_id');
    }
    
    // belongs to a cmts - see BaseModel for explanation
    public function view_belongs_to ()
    {
        return $this->cmts;
    }

    /**
     * BOOT:
     * - init cmts observer
     */
    public static function boot()
    {
        parent::boot();

        IpPool::observe(new IpPoolObserver);
        IpPool::observe(new \SystemdObserver);
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
        // fetch cmts object that is related to the created ippool and make dhcp conf
        // if (isset($pool->cmts))
            $pool->cmts->make_dhcp_conf();
    }

    public function updated($pool)
    {
       $pool->cmts->make_dhcp_conf();

        // make dhcp conf of old cmts if relation got changed
        if ($pool["original"]["cmts_id"] != $pool["attributes"]["cmts_id"])
            $cmts_old = Cmts::find($pool["original"]["cmts_id"])->make_dhcp_conf();
    }

    public function deleted($pool)
    {
        $pool->cmts->make_dhcp_conf();
    }
}