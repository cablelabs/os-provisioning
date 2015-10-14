<?php

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
	protected $fillable = ['cmts_gw_id', 'type', 'net', 'netmask', 'ip_pool_start', 'ip_pool_end', 'router_ip', 'broadcast_ip', 'dns1_ip', 'dns2_ip', 'dns3_ip', 'optional'];


    /**
     * Relationships:
     */

    public function cmts ()
    {
        return $this->belongsTo('CmtsGw');
    }
    
}