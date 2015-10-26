<?php

namespace Models;
use DB;

class IpPool extends \BaseModel {

    // The associated SQL table for this Model
    protected $table = 'ippool';

    // Add your validation rules here
    public static function rules($id = null)
    {
        return array(
            'net' => 'ip',
            'netmask' => 'ip',
            'ip_pool_start' => 'ip' ,
            'ip_pool_end' => 'ip' ,
            'router_ip' => 'ip' ,
            'broadcast_ip' => 'ip' ,
            'dns1_ip' => 'ip' ,
            'dns2_ip' => 'ip' ,
            'dns3_ip' => 'ip'
        );
    }

	// Don't forget to fill this array
	protected $fillable = ['cmts_id', 'type', 'net', 'netmask', 'ip_pool_start', 'ip_pool_end', 'router_ip', 'broadcast_ip', 'dns1_ip', 'dns2_ip', 'dns3_ip', 'optional'];


    /**
     * Returns all cmts hostnames for ip pools as an array
     */
    private function cmts_hostnames ()
    {
        return DB::table('cmts')->select('id', 'hostname')->get();
    }


    /**
     * Returns the data array for all views of the model
     */
    public function html_list_array ()
    {
        $ret = array (
                'hostnames' => $this->html_list($this->cmts_hostnames(), 'hostname')
            );
        return $ret;
    }



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