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
	public static function view_headline()
	{
		return 'IP-Pools';
	}

	// View Icon
	public static function view_icon()
	{
	  return '<i class="fa fa-tags"></i>';
	}

	// link title in index view
	public function view_index_label()
	{
		$bsclass = $this->get_bsclass();

		return ['index' => [$this->id, $this->cmts->hostname, $this->type, $this->net, $this->netmask, $this->router_ip, $this->description],
				'index_header' => ['ID', 'CMTS', 'Type of Pool', 'IP network', 'IP netmask', 'IP router', 'Description'],
				'bsclass' => $bsclass,
				'header' => $this->type.': '.$this->net.' / '.$this->netmask];
	}

	// AJAX Index list function
	// generates datatable content and classes for model
	public function view_index_label_ajax()
	{
		$bsclass = $this->get_bsclass();

		return ['table' => $this->table,
				'index_header' => [$this->table.'.id','cmts.hostname', $this->table.'.type', $this->table.'.net', $this->table.'.netmask', $this->table.'.router_ip', $this->table.'.description'],
				'header' =>  $this->type.': '.$this->net.' / '.$this->netmask,
				'bsclass' => $bsclass,
				'eager_loading' => ['cmts']];
	}

	public function get_bsclass()
	{
		$bsclass = 'success';
			
		if ($this->type == 'CPEPub')
			$bsclass = 'warning';
		if ($this->type == 'CPEPriv')
			$bsclass = 'info';
		if ($this->type == 'MTA')
			$bsclass = 'danger';

		return $bsclass;
	}


	/**
	 * Returns all cmts hostnames for ip pools as an array
	 */
	public function cmts_hostnames ()
	{
		return DB::table('cmts')->select('id', 'hostname')->get();
	}


	/*
	 * Return the corresponding network size to the netmask,
	 * e.g. 255.255.255.240 will return 28 as integer – means /28 netmask
	 */
	public function size ()
	{
		// this is crazy shit from http://php.net/manual/de/function.ip2long.php
		$long = ip2long($this->netmask);
		$base = ip2long('255.255.255.255');

		return 32-log(($long ^ $base)+1,2);
	}

	/*
	 * Returns true if provisioning route to $this pool exists, otherwise false
	 */
	public function ip_route_prov_exists()
	{
		return (strlen(exec ('ip route show '.$this->net.'/'.$this->size().' via '.$this->router_ip)) == 0 ? false : true);
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
		IpPool::observe(new \App\SystemdObserver);
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
