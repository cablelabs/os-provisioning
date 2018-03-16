<?php

namespace Modules\ProvBase\Entities;

use DB;
use Modules\ProvBase\Entities\Cmts;

class IpPool extends \BaseModel {

	private static $static_ip_file = '/etc/dhcp/nmsprime/public-static.conf';

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

	// AJAX Index list function
	// generates datatable content and classes for model
	public function view_index_label()
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
		return (strlen(exec ('ip route show '.$this->net.'/'.$this->size().' via '.$this->cmts->ip)) == 0 ? false : true);
	}


	/*
	 * Return true if $this->router_ip is online, otherwise false
	 * This implies that the CMTS Pool should be set correctly in the CMTS
	 */
	public function ip_route_online ()
	{
		// Ping: Only check if device is online
		exec ('sudo ping -c1 -i0 -w1 '.$this->router_ip, $ping, $ret);
		return $ret ? false : true;
	}

	/**
	 * Return 'secondary' if this pool is not the first CM pool of the CMTS,
	 * otherwise an empty string
	 *
	 * @return String
	 *
	 * @author Ole Ernst
	 */
	public function is_secondary ()
	{
		$cm_pools = $this->cmts->ippools->filter(function ($item) {
			return $item->type == 'CM';
		});

		if($cm_pools->isEmpty() || $this->id != $cm_pools->first()->id)
			return 'secondary';

		return '';
	}

	/**
	 * Return the range string according to the IpPool. We need to cut out public
	 * CPE IP addresses, which have been statically assigned - so that they won't
	 * be given out to multiple CPEs
	 *
	 * @return String
	 *
	 * @author Ole Ernst
	 */
	public function get_range()
	{
		$ret = "\t\t\trange ".$this->ip_pool_start.' '.$this->ip_pool_end.";\n";

		if ($this->type != 'CPEPub' || !file_exists(self::$static_ip_file) || !filesize(self::$static_ip_file))
			return $ret;

		preg_match_all('/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/', file_get_contents(self::$static_ip_file), $static);
		// there are no static ip addresses defined, return normal range string
		if (empty($static[0]))
			return $ret;

		$ret = '';
		$static = array_map(function($ip) { return ip2long($ip); }, $static[0]);
		$all = range(ip2long($this->ip_pool_start), ip2long($this->ip_pool_end));
		foreach (array_diff($all, $static) as $ip)
			$ret .= "\t\t\trange ".long2ip($ip).";\n";

		return $ret;
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
