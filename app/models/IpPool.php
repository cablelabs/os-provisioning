<?php

namespace Models;

class IpPool extends \Eloquent {

	// Add your validation rules here
	public static $rules = [
        'net' => 'ip',
        'netmask' => 'ip',
        'ip_pool_start' => 'ip' ,
        'ip_pool_end' => 'ip' ,
        'router_ip' => 'ip' ,
        'broadcast_ip' => 'ip' ,
        'dns1_ip' => 'ip' ,
        'dns2_ip' => 'ip' ,
        'dns3_ip' => 'ip'
	];

	// Don't forget to fill this array
	protected $fillable = ['cmts_id', 'type', 'net', 'netmask', 'ip_pool_start', 'ip_pool_end', 'router_ip', 'broadcast_ip', 'dns1_ip', 'dns2_ip', 'dns3_ip', 'optional'];


    /**
     * Relationships:
     */

    public function cmts()
    {
        return $this->belongsTo('Models\Cmts', 'cmts_id');
    }


        /**
     * BOOT:
     * - init cmts observer
     */
    public static function boot()
    {
        parent::boot();

        IpPool::observe(new IpPoolObserver);
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
        $cmts = $pool->cmts;
        $cmts->make_dhcp_conf();
    }

    public function updated($pool)
    {
        $cmts = $pool->cmts;
        $cmts->make_dhcp_conf();
    }

    public function deleted($pool)
    {
        $cmts = $pool->cmts;
        $cmts->make_dhcp_conf();
    }
}