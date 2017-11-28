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

	// View Icon
	public static function view_icon()
	{
		return '<i class="fa fa-server"></i>';
	}

	// link title in index view
	public function view_index_label()
	{
		$bsclass = $this->get_bsclass();

		return ['index' => [$this->id, $this->hostname, $this->ip, $this->company, $this->type],
				'index_header' => ['ID', 'Hostname', 'IP address', 'Company', 'Type'],
				'bsclass' => $bsclass,
				'header' => $this->hostname];
	}

	// AJAX Index list function
	// generates datatable content and classes for model
	public function view_index_label_ajax()
	{
		$bsclass = $this->get_bsclass();

		return ['table' => $this->table,
				'index_header' => [$this->table.'.id', $this->table.'.hostname', $this->table.'.ip', $this->table.'.company', $this->table.'.type'],
				'header' =>  $this->hostname,
				'bsclass' => $bsclass,
				'order_by' => ['0' => 'asc']];
	}

	public function get_bsclass()
	{
		$bsclass = 'success';

		// TODO: use cmts state value
		if ($this->state == 1)
			$bsclass = 'warning';
		if ($this->state == 2)
			$bsclass = 'danger';
		return $bsclass;
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
		// related IP Pools
		$ret['Base']['IpPool']['class'] = 'IpPool';
		$ret['Base']['IpPool']['relation'] = $this->ippools;

		// Routing page
		$this->prep_cmts_config_page();
		$ret['Base']['Config']['view']['vars'] = ['cb' => $this]; // cb .. CMTS blade
		$ret['Base']['Config']['view']['view'] = 'provbase::Cmts.overview';

		return $ret;
	}


	/*
	 * Return the CMTS config as clear text
	 */
	public function get_raw_cmts_config()
	{
		$view_var = $this;
		$cb = $this;

		return strip_tags(view('provbase::Cmtsblade.'.strtolower($this->company), compact('cb', 'view_var'))->render());
	}


	/*
	 * create a cisco encrypted password, like $1$fUW9$EAwpFkkbCTUUK8MpRS1sI0
	 *
	 * See: https://serverfault.com/questions/26188/code-to-generate-cisco-secret-password-hashes/46399
	 *
	 * NOTE: dont encrypt if CMTS_SAVE_ENCRYPTED_PASSWORDS is set in env file
	 */
	public function create_cisco_encrypt ($psw)
	{
		// Dont encrypt password, it is still encrypted
		if (env('CMTS_SAVE_ENCRYPTED_PASSWORDS', false))
			return $psw;

		exec ('openssl passwd -salt `openssl rand -base64 3` -1 "'.$psw.'"', $output);
		return $output[0];
	}


	/*
	 * CMTS Config Page:
	 * Prepare Cmts Config Variables
	 *
	 * They are required in Cmtsblade's
	 *
	 * NOTE: this will fit 90% of generic installations
	 */
	public function prep_cmts_config_page()
	{
		// password section
		$this->enable_secret = $this->create_cisco_encrypt(env('CMTS_ENABLE_SECRET', 'admin'));
		$this->admin_psw = $this->create_cisco_encrypt(env('CMTS_ADMIN_PASSWORD', 'admin'));
		// NOTE: this is quit insecure and should be a different psw that the encrypted ones above!
		$this->vty_psw = env('CMTS_VTY_PASSWORD', 'adminvty');

		// type specific settings
		switch ($this->type) {
			case 'ubr7225':
				$this->interface = 'GigabitEthernet0/1';
				break;

			case 'ubr10k':
				$this->interface = 'GigabitEthernet1/0/0';
				break;

			default:
				$this->interface = 'GigabitEthernet0/1';
				break;
		}

		// get provisioning IP and interface
		$this->prov_ip = ProvBase::first()->provisioning_server;
		exec ('ip a | grep '.$this->prov_ip.' | tr " " "\n" | tail -n1', $prov_if);
		$this->prov_if = (isset($prov_if[0]) ? $prov_if[0] : 'eth');

		$this->domain = ProvBase::first()->domain_name;
		$this->router_ip = env('CMTS_DEFAULT_GW', '10.255.0.254');
		$this->netmask = env('CMTS_IP_NETMASK', '255.255.255.0');
		$this->tf_net_1 = env('CMTS_TRANSFER_NET', '10.255.0.1'); // servers with /24
		$this->nat_ip = env('CMTS_NAT_IP', '10.255.0.2'); // second server ip is mostlikely NAT

		$this->snmp_ro = $this->get_ro_community();
		$this->snmp_rw = $this->get_rw_community();

		// Help section: onhover
		$this->enable_secret = '<span title="CMTS_ENABLE_SECRET and CMTS_SAVE_ENCRYPTED_PASSWORDS"><b>'.$this->enable_secret.'</b></span>';
		$this->admin_psw = '<span title="CMTS_ADMIN_PASSWORD and CMTS_SAVE_ENCRYPTED_PASSWORDS"><b>'.$this->admin_psw.'</b></span>';
		$this->vty_psw = '<span title="CMTS_VTY_PASSWORD"><b>'.$this->vty_psw.'</b></span>';
		$this->prov_ip = '<span title="Set in Global Config Page / Provisioning / Provisioning Server IP"><b>'.$this->prov_ip.'</b></span>';
		$this->interface = '<span title="Depending on CMTS Device Company and Type"><b>'.$this->interface.'</b></span>';
		$this->domain = '<span title="Set in Global Config Page / Provisioning / Domain Name"><b>'.$this->domain.'</b></span>';
		$this->router_ip = '<span title="CMTS_DEFAULT_GW"><b>'.$this->router_ip.'</b></span>';
		$this->netmask = '<span title="CMTS_IP_NETMASK"><b>'.$this->netmask.'</b></span>';
		$this->tf_net_1 = '<span title="CMTS_TRANSFER_NET"><b>'.$this->tf_net_1.'</b></span>';
		$this->nat_ip = '<span title="CMTS_NAT_IP"><b>'.$this->nat_ip.'</b></span>';
		$this->snmp_ro = '<span title="Set in CMTS page or Global Config Page / Provisioning if empty in CMTS page"><b>'.$this->snmp_ro.'</b></span>';
		$this->snmp_rw = '<span title="Set in CMTS page or Global Config Page / Provisioning if empty in CMTS page"><b>'.$this->snmp_rw.'</b></span>';
	}


	/**
	 * Get SNMP read-only community string
	 *
	 * @author Ole Ernst
	 */
	public function get_ro_community()
	{
		if ($this->community_ro)
			return $this->community_ro;
		else
			return ProvBase::first()->ro_community;
	}


	/**
	 * Get SNMP read-write community string
	 *
	 * @author Ole Ernst
	 */
	public function get_rw_community()
	{
		if ($this->community_rw)
			return $this->community_rw;
		else
			return ProvBase::first()->rw_community;
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
		$snrs = json_decode(\Storage::get("data/provbase/us_snr/$this->hostname.json"), true);
		if(array_key_exists($ip, $snrs))
			return $snrs[$ip];

		return ['SNR not found'];
	}


	/**
	 * Store US SNR values for all modems once every 5 minutes
	 * this greatly reduces the cpu load on the cmts
	 *
	 * @author Ole Ernst
	 */
	public function store_us_snrs()
	{
		$ret = [];
		$com = $this->get_ro_community();

		snmp_set_valueretrieval(SNMP_VALUE_LIBRARY);
		snmp_set_quick_print(true);
		$ips = snmp2_real_walk($this->ip, $com, '.1.3.6.1.4.1.4491.2.1.20.1.3.1.5');
		$snrs = snmp2_real_walk($this->ip, $com, '.1.3.6.1.4.1.4491.2.1.20.1.4.1.4');

		foreach ($ips as $i_key => $i_val) {
			$i_key = last(explode('.', $i_key));
			$tmp = array_filter($snrs, function($s_key) use($i_key) {
				return strpos($s_key, $i_key) !== false;
			}, ARRAY_FILTER_USE_KEY);
			$ret[long2ip(hexdec($i_val))] = ArrayHelper::ArrayDiv(array_values($tmp));
		}
		\Storage::put("data/provbase/us_snr/$this->hostname.json", json_encode($ret));
	}


	/**
	 * Get US modulations of the respective channel ID
	 *
	 * @param ch_ids: Array of channel IDs
	 * @return Array of corresponding modulations used (docsIfCmtsModType)
	 *
	 * @author Ole Ernst
	 */
	public function get_us_mods($ch_ids)
	{
		$mods = [];
		// get all channel IDs of the CMTS
		$idxs = snmprealwalk($this->ip, $this->get_ro_community(), '.1.3.6.1.2.1.10.127.1.1.2.1.1');

		// intersect all channel IDs with the ones used by the modem (supplied as method argument)
		foreach(array_intersect($idxs, $ch_ids) as $key => $val) {
			$key = explode('.', $key);
			// get the modulation profile ID used for this channel
			$mod_prof = snmpwalk($this->ip, $this->get_ro_community(), '.1.3.6.1.2.1.10.127.1.1.2.1.4.'.end($key));
			// get all modulations of this profile
			$mod = snmpwalk($this->ip, $this->get_ro_community(), '.1.3.6.1.2.1.10.127.1.3.5.1.4.'.array_pop($mod_prof));
			// only add the last one, as this is used for user data
			$mods[] = array_pop($mod);
		}

		return $mods;
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
		$file_dhcp_conf = '/etc/dhcp/nmsprime/cmts_gws.conf';
		$file = '/etc/dhcp/nmsprime/cmts_gws/'.$this->hostname.'.conf';

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
					// $data .= "\n\t\t\t".'allow unknown-clients;';
					// $data .= "\n\t\t\t".'allow known-clients;';
					break;

				case 'MTA':
					$data .= "\n\t\t\t".'allow members of "MTA";';
					// $data .= "\n\t\t\t".'allow known-clients;';
					break;

				default:
					// code...
					break;
			}

			$data .= "\n\t\t".'}';

			// append additional options
			if ($options)
					$data .= "\n\n\t\t".$options;


			$data .= "\n\t".'}'."\n";

			$data .= "\n\tsubnet $router netmask 255.255.255.255\n\t{";
			$data .= "\n\t\tallow leasequery;";
			$data .= "\n\t}\n";

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

		$file = '/etc/dhcp/nmsprime/cmts_gws/'.$this->hostname.'.conf';
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
		$include_str = '/etc/dhcp/nmsprime/cmts_gws/';

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
		if (\PPModule::is_active ('ProvMon'))
			\Artisan::call('nms:cacti', ['--modem-id' => 0, '--cmts-id' => $cmts->id]);
		$cmts->make_dhcp_conf();

		// write CMTS config to /tftpboot/cmts
		File::put('/tftpboot/cmts/'.$cmts->hostname.'.cfg', $cmts->get_raw_cmts_config());
	}

	public function updating($cmts)
	{
		$tmp = Cmts::find($cmts->id);
		$tmp->delete_cmts();
	}

	public function updated($cmts)
	{
		$cmts->make_dhcp_conf();

		// write CMTS config to /tftpboot/cmts
		File::put('/tftpboot/cmts/'.$cmts->hostname.'.cfg', $cmts->get_raw_cmts_config());
	}

	public function deleted($cmts)
	{
		// delete the conf file and the include statement in /etc/dhcp/dhcpd.conf
		$cmts->delete_cmts();
	}
}
