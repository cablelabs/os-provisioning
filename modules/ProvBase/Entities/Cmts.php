<?php

namespace Modules\ProvBase\Entities;

use File;
use DB;

class Cmts extends \BaseModel {

	// The associated SQL table for this Model
	public $table = 'cmts';

	// Add your validation rules here
	public static function rules($id = null)
    {
        return array(
			'hostname' => 'unique:cmts,hostname,'.$id  	// unique: table, column
        );
    }

	// Don't forget to fill this array
	protected $fillable = ['hostname', 'type', 'ip', 'community_rw', 'community_ro', 'company', 'state', 'monitoring'];
	// columns in database that shall not be able to alter
	// protected $guarded = [];


	// Name of View
	public static function get_view_header()
	{
		return 'CMTS';
	}

	// link title in index view
	public function get_view_link_title()
	{
		return $this->hostname;
	}

    /**
     * BOOT - init cmts observer
     */
    public static function boot()
    {
        parent::boot();

        Cmts::observe(new CmtsObserver);
        Cmts::observe(new \SystemdObserver);
    }


    /**
     * Relationships:
     */

    public function ippools ()
    {
        return $this->hasMany('Modules\ProvBase\Entities\IpPool');
    }

    // returns all objects that are related to a cmts
    public function view_has_many()
    {
    	return array(
    		'IpPool' => $this->ippools
    	);
    }


	/**
	 * auto generates the dhcp conf file for a specified cmts and 
	 * adds the appropriate include statement in dhcpd.conf
	 * 
	 * (description is automatically taken by phpdoc)
	 *
	 * @author Nino Ryschawy
	 * @param 
	 * @return void
	 */
	public function make_dhcp_conf ()
	{
		$file_dhcp_conf = '/etc/dhcp/dhcpd.conf';
		$file = '/etc/dhcp/nms/cmts_gws/'.$this->hostname.'.conf';

		if ($this->id == '0')
			return -1;

		$ippools = $this->ippools;

		// if a cmts doesn't have an ippool the file has to be empty
		if (!$ippools->has('0'))
		{
			File::put($file, '');
			goto _exit;
		}

		File::put($file, 'shared-network "'.$this->hostname.'"'."\n".'{'."\n");

		foreach ($ippools as $pool) {

			if ($pool->id == 0)
				continue;

			$subnet = $pool->net;
			$netmask = $pool->netmask;
			$broadcast_addr = $pool->broadcast_ip;
			$range = $pool->ip_pool_start.' '.$pool->ip_pool_end;
			$router = $pool->router_ip;
			$type = $pool->type;
			$options = $pool->optional;


			$data = "\n\t".'subnet '.$subnet.' netmask '.$netmask."\n\t".'{';
			$data .= "\n\t\t".'option routers '.$router.';';
			$data .= "\n\t\t".'option broadcast-address '.$broadcast_addr.';';
			$data .= "\n\n\t\t".'pool'."\n\t\t".'{';
			$data .= "\n\t\t\t".'range '.$range.';'."\n";

			switch ($type)
			{
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
					$data .= "\n\t\t\t".'allow unknown-clients;';
					// $data .= "\n\t\t\t".'allow known-clients;';
					break;

				case 'MTA':
					$data .= "\n\t\t\t".'allow members of "MTA";';
					// $data .= "\n\t\t\t".'allow known-clients;';
					break;

				default:
					# code...
					break;
			}

			$data .= "\n\t\t".'}';

			// append additional options
			if ($options)
					$data .= "\n\n\t\t".$options;

			
			$data .= "\n\t".'}'."\n";

			File::append($file, $data);
		}

		File::append($file, '}'."\n");


		// append include statement in dhcpd.conf if not yet done
		$handle = fopen($file_dhcp_conf, 'r');
		$existent = false;

		// search for file-string
		while (($buffer = fgets($handle)) !== false)
		{
			if (strpos($buffer, $file) !== false)
			{
				$existent = true;
				break;
			}
		}

		if (!$existent)
		{
			File::append($file_dhcp_conf, "\n".'include "'.$file.'";');
		}


_exit:
		// chown for future writes in case this function was called from CLI via php artisan nms:dhcp that changes owner to 'root'
        system('/bin/chown -R apache /etc/dhcp/');
	}

	/**
	 * Deletes the calling object/cmts in DB and removes the include statement from the global dhcpd.conf
	 * Also sets the related IP-Pools to cmts_id=0
	 *
	 * @author Nino Ryschawy
	 * @param
	 * @return void 
	 */
	public function delete_cmts()
	{

		$file = '/etc/dhcp/nms/cmts_gws/'.$this->hostname.'.conf';
		if (file_exists($file)) unlink($file);

		$lines = file('/etc/dhcp/dhcpd.conf');

		foreach($lines as $key => $line)
		{
			// line found
			if(strpos($line, $file) !== false)
			{
				if ($lines[$key-1] == "")
					$lines[$key-1] = str_replace(PHP_EOL, "", $lines[$key-1]);
				unset($lines[$key]);
			}
		}

		$data = implode(array_values($lines));

		$file_dhcp_conf = fopen('/etc/dhcp/dhcpd.conf', 'w');
		fwrite($file_dhcp_conf, $data);
		fclose($file_dhcp_conf);

		// set all relevant ip pools to cmts_id = 0 (to first cmts_id under development)
		// TODO: set first_cmts_id to zero!
		$first_cmts_id = Cmts::first()->id;
		DB::update('UPDATE ippool SET cmts_id='.$first_cmts_id.' where cmts_id='.$this->id.';');

	}

	/**
	 * Deletes all cmts include statements in global dhcpd.conf
	 *
	 * @return
	 * @author Nino Ryschawy
	 */
	public function del_cmts_includes()
	{
		$file_path = '/etc/dhcp/dhcpd.conf';
		$include_str = '/etc/dhcp/nms/cmts_gws/';

		// copy file as backup
		copy($file_path, $file_path.'_backup');

		$lines = file($file_path);
		$data = '';
		$bool = true;
		$i = 0;

		foreach($lines as $key => $line)
		{
			// if it's an cmts include line
			if(strpos($line, $include_str) !== false)
			{
				// remove all empty lines only the first time an cmts include statement was found
				do
				{
					if (!$bool)
						break;
					$lines[$key - $i] = str_replace(PHP_EOL, "", $lines[$key - $i]);
					$i++;
				} while (($lines[$key - $i] == "\n") || ($lines[$key - $i] == ""));
				
				unset($lines[$key]);
				$bool = false;

			}
		}

		$data = implode(array_values($lines));

		$file = fopen($file_path, 'w');
		fwrite($file, $data);
		fclose($file);

	}

}


/**
 * CMTS Observer Class
 * Handles changes on CMTS Gateways
 *
 * can handle   'creating', 'created', 'updating', 'updated',
 *              'deleting', 'deleted', 'saving', 'saved',
 *              'restoring', 'restored',
 */
class CmtsObserver
{
    public function created($cmts)
    {
    	// dd(\Route::getCurrentRoute()->getActionName(), $this);
  		// only create new config file
        $cmts->make_dhcp_conf();
    }

    public function updating($cmts)
    {
    	$tmp = Cmts::find($cmts->id);
    	$tmp->delete_cmts();
    }

    public function updated($cmts)
    {
        $cmts->make_dhcp_conf();
    }

    public function deleted($cmts)
    {
    	// delete the conf file and the include statement in /etc/dhcp/dhcpd.conf
    	$cmts->delete_cmts();
    }
}