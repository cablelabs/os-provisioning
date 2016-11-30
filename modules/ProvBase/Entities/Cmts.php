<?php

namespace Modules\ProvBase\Entities;

use File;
use DB;
use Acme\php\ArrayHelper;

class Cmts extends \BaseModel {

	// The associated SQL table for this Model
	public $table = 'cmts';

	// Add your validation rules here
	public static function rules($id = null)
	{
		return array(
			'hostname' => 'required|unique:cmts,hostname,'.$id.',id,deleted_at,NULL',  	// unique: table, column, exception , (where clause)
			'company' => 'required',
		);
	}


	// Name of View
	public static function view_headline()
	{
		return 'CMTS';
	}

	// link title in index view
	public function view_index_label()
	{
		$bsclass = 'success';

		// TODO: use cmts state value
		if ($this->state == 1)
			$bsclass = 'warning';
		if ($this->state == 2)
			$bsclass = 'danger';

		return ['index' => [$this->id, $this->hostname, $this->ip, $this->company, $this->type],
				'index_header' => ['ID', 'Hostname', 'IP address', 'Company', 'Type'],
				'bsclass' => $bsclass,
				'header' => $this->hostname];
	}


	/**
	 * BOOT - init cmts observer
	 */
	public static function boot()
	{
		parent::boot();

		Cmts::observe(new CmtsObserver);
		Cmts::observe(new \App\SystemdObserver);
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
	 * Get US SNR of a registered CM
	 *
	 * @param ip: ip address of cm
	 *
	 * @author Nino Ryschawy
	 */
	public function get_us_snr($ip)
	{
		// find oid of corresponding modem on cmts and get the snr
		$conf = snmp_get_valueretrieval();
		if ($this->community_ro != '')
			$com = $this->community_ro;
		else
			$com = ProvBase::first()->ro_community;

		// we need to change the value retrievel for snmprealwalk()
		snmp_set_valueretrieval(SNMP_VALUE_OBJECT);
		try
		{
			$modem_ips = snmprealwalk($this->ip, $com, '1.3.6.1.4.1.4491.2.1.20.1.3.1.5');
		}
		catch(\Exception $e)
		{
			snmp_set_valueretrieval($conf);
			return ['No response of CMTS'];
		}
		snmp_set_valueretrieval($conf);
		foreach ($modem_ips as $oid => $value)
		{
			$cmts_cm_ip = long2ip('0x'.str_replace(["\"", " "], '', $value->value));
			if ($cmts_cm_ip == $ip)
				return ArrayHelper::ArrayDiv(snmpwalk($this->ip, $com, str_replace('.1.3.6.1.4.1.4491.2.1.20.1.3.1.5', '1.3.6.1.4.1.4491.2.1.20.1.4.1.4', $oid)));
		}
		return ['Could not find CMTS'];
	}


	/**
	 * auto generates the dhcp conf file for a specified cmts and
	 * adds the appropriate include statement in dhcpd.conf
	 *
	 * (description is automatically taken by phpdoc)
	 *
	 * TODO: improve performance by collecting data first and put to file once at the end
	 *
	 * @author Nino Ryschawy
	 */
	public function make_dhcp_conf ()
	{
		$file_dhcp_conf = '/etc/dhcp/dhcpd.conf';
		$file = '/etc/dhcp/nms/cmts_gws/'.$this->hostname.'.conf';

		if ($this->id == 0)
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
			$dns['1'] = $pool->dns1_ip;
			$dns['2'] = $pool->dns2_ip;
			$dns['3'] = $pool->dns3_ip;


			$data = "\n\t".'subnet '.$subnet.' netmask '.$netmask."\n\t".'{';
			$data .= "\n\t\t".'option routers '.$router.';';
			if ($broadcast_addr != '')
				$data .= "\n\t\t".'option broadcast-address '.$broadcast_addr.';';
			if ($dns['1'] != '' || $dns['2'] != '' || $dns['3'] != '')
			{
				$data .= "\n\t\toption domain-name-servers ";
				$data_tmp = '';
				foreach ($dns as $ip)
				{
					if ($ip != '')
						$data_tmp .= "$ip, ";
				}
				$pos = strrpos($data_tmp, ',');
				$data .= substr_replace($data_tmp, '', $pos, 1).";";
			}
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
	}

	/**
	 * Deletes all cmts include statements in global dhcpd.conf
	 *
	 * @return
	 * @author Nino Ryschawy
	 */
	public static function del_cmts_includes()
	{
		$file_path   = '/etc/dhcp/dhcpd.conf';
		$include_str = '/etc/dhcp/nms/cmts_gws/';

		// copy file as backup
		copy($file_path, $file_path.'_backup');

		$lines = file($file_path);
		$data = '';
		$bool = false;
		$i = 0;

		foreach($lines as $key => $line)
		{
			// if it's an cmts include line
			if(strpos($line, $include_str) !== false)
			{
				// remove all empty lines only the first time an cmts include statement was found
				do
				{
					if ($bool)
						break;
					$lines[$key - $i] = str_replace(PHP_EOL, "", $lines[$key - $i]);
					$i++;
				} while (($lines[$key - $i] == "\n") || ($lines[$key - $i] == ""));

				unset($lines[$key]);
				$bool = true;

			}
		}

		$data = implode(array_values($lines));

		\File::put($file_path, $data);

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
		// dd($cmts);
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