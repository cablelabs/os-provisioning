<?php

namespace Modules\ProvBase\Entities;

use File;
use Log;
use Exception;
use Modules\ProvBase\Entities\Qos;
use Modules\ProvBase\Entities\ProvBase;

class Modem extends \BaseModel {

	// The associated SQL table for this Model
	public $table = 'modem';


	// Add your validation rules here
	// see: http://stackoverflow.com/questions/22405762/laravel-update-model-with-unique-validation-rule-for-attribute
	public static function rules($id = null)
	{
		return array(
			'mac' => 'required|mac|unique:modem,mac,'.$id.',id,deleted_at,NULL'
		);
	}


	// Name of View
	public static function view_headline()
	{
		return 'Modems';
	}

	// link title in index view
	public function view_index_label()
	{
		$bsclass = 'success';
		$status = $this->status.' dBmV';

		switch ($this->get_state('int'))
		{
			case 0:	$bsclass = 'success'; break; // online
			case 1: $bsclass = 'warning'; break; // warning
			case 2: $bsclass = 'danger'; break; // critical
			case 3: $bsclass = 'danger'; $status = 'offline'; break; // offline

			default: $bsclass = 'danger'; break;
		}

		return ['index' => [$this->id, $this->mac, $this->lastname, $this->zip, $this->city, $this->street, $status],
		        'index_header' => ['Modem Number', 'MAC address', 'Lastname', 'Postcode', 'City', 'Street', 'US level'],
		        'bsclass' => $bsclass,
		        'header' => $this->id.' - '.$this->mac];
	}



	/**
	 * return all Configfile Objects for CMs
	 */
	public function configfiles ()
	{
		return Configfile::where('device', '=', 'CM')->where('public', '=', 'yes')->get();
	}

	/**
	 * return all Configfile Objects for CMs
	 */
	public function qualities ()
	{
		return QoS::all();
	}


	/**
	 * all Relationships:
	 */
	public function configfile ()
	{
		return $this->belongsTo('Modules\ProvBase\Entities\Configfile');
	}

	public function qos()
	{
		return $this->belongsTo("Modules\ProvBase\Entities\Qos");
	}

	public function contract()
	{
		return $this->belongsTo('Modules\ProvBase\Entities\Contract', 'contract_id');
	}

	public function contracts()
	{
		return Contract::get();
	}

	public function mtas()
	{
		if ($this->module_is_active('ProvVoip'))
			return $this->hasMany('Modules\ProvVoip\Entities\Mta');

		return null;
	}

	public function tree()
	{
		if ($this->module_is_active('HfcBase'))
			return $this->belongsTo('Modules\HfcBase\Entities\Tree');

		return null;
	}


	/*
	 * Relation Views
	 */
	public function view_belongs_to ()
	{
		return $this->contract;
	}

	public function view_has_many()
	{
		if ($this->module_is_active('ProvVoip'))
			return array(
					'Mta' => $this->mtas
				);

		return array();
	}


	/**
	 * BOOT:
	 * - init modem observer
	 */
	public static function boot()
	{
		parent::boot();

		Modem::observe(new \SystemdObserver);
		Modem::observe(new ModemObserver);
	}


	/**
	 * Define global constants for dhcp config files of modems (private and public)
	 */
	const CONF_FILE_PATH = '/etc/dhcp/nms/modems-host.conf';
	const CONF_FILE_PATH_PUB = '/etc/dhcp/nms/modems-clients-public.conf';


	/**
	 * Returns the config file entry string for a cable modem in dependency of private or public ip
	 *
	 * TODO: use object context instead of parameters (Torsten)
	 *
	 * @author Nino Ryschawy
	 */
	private function generate_cm_update_entry($id, $mac)
	{
			return "\n".'host cm-'.$id.' { hardware ethernet '.$mac.'; filename "cm/cm-'.$id.'.cfg"; ddns-hostname "cm-'.$id.'"; }';
	}
	private function generate_cm_update_entry_pub($id, $mac)
	{
			return "\n".'subclass "Client-Public" '.$mac.'; # CM id:'.$id;
	}


	/**
	 * Deletes the configfiles with all modem dhcp entries - used to refresh the config through artisan nms:dhcp command
	 *
	 * @author Nino Ryschawy
	 */
	public function del_dhcp_conf_files()
	{
		if (file_exists(self::CONF_FILE_PATH)) unlink(self::CONF_FILE_PATH);
		if (file_exists(self::CONF_FILE_PATH_PUB)) unlink(self::CONF_FILE_PATH_PUB);
	}



	/**
	 * Make DHCP config files for all CMs including EPs - used in dhcpCommand after deleting
	 * the config files with all entries
	 *
	 * TODO: make static function (class context not object)
	 *
	 * @author Torsten Schmidt
	 */
	public function make_dhcp_cm_all ()
	{
		$this->del_dhcp_conf_files();

		// Log
		Log::info('dhcp: update '.self::CONF_FILE_PATH.', '.self::CONF_FILE_PATH_PUB);

		foreach (Modem::all() as $modem)
		{
			$id	= $modem->id;
			$mac   = $modem->mac;

			if ($id == 0)
				continue;

			// all
			$data = $modem->generate_cm_update_entry($id, $mac);
			$ret = File::append(self::CONF_FILE_PATH, $data);
			if ($ret === false)
				die("Error writing to file");

			// public ip
			if ($modem->public)
			{
				$data = $modem->generate_cm_update_entry_pub($id, $mac);
				$ret = File::append(self::CONF_FILE_PATH_PUB, $data);
				if ($ret === false)
					die("Error writing to file");
			}
		}

		// chown for future writes in case this function was called from CLI via php artisan nms:dhcp that changes owner to 'root'
		system('/bin/chown -R apache /etc/dhcp/');

		return ($ret > 0 ? true : false);
	}


	/**
	 * Make Configfile for a single CM
	 */
	public function make_configfile ()
	{
		$modem = $this;
		$id	= $modem->id;
		$mac   = $modem->mac;
		$host  = $modem->hostname;

		/* Configfile */
		$dir		= '/tftpboot/cm/';
		$cf_file	= $dir."cm-$id.conf";
		$cfg_file   = $dir."cm-$id.cfg";

		$cf = $modem->configfile;

		if (!$cf)
			return false;

		$text = "Main\n{\n\t".$cf->text_make($modem, "modem")."\n}";
		$ret  = File::put($cf_file, $text);


		if ($ret === false)
				die("Error writing to file");

		Log::info('configfile: update modem '.$this->hostname);
		Log::info("configfile: /usr/local/bin/docsis -e $cf_file $dir/../keyfile $cfg_file");
		// if (file_exists($cfg_file))
		//	 unlink($cfg_file);

		// "&" to start docsis process in background improves performance but we can't reliably proof if file exists anymore
		exec("/usr/local/bin/docsis -e $cf_file $dir/../keyfile $cfg_file >/dev/null 2>&1 &", $out);

		// change owner in case command was called from command line via php artisan nms:configfile that changes owner to root
		system('/bin/chown -R apache /tftpboot/cm');

		// docsis tool always returns 0 -> so we need to proof if that way (only when docsis isnt started in background)
		// if (file_exists($cfg_file))
		//	 return true;
		// return false;

		return true;
	}

	/**
	 * Make all Configfiles
	 */
	public function make_configfile_all()
	{
		$m = Modem::all();
		foreach ($m as $modem)
		{
			if ($modem->id == 0)
				continue;
			if (!$modem->make_configfile())
				Log::warning("failed to build/write configfile for modem cm-".$modem->id);
		}

		return true;
	}

	/**
	 * Deletes Configfile of a modem
	 */
	public function delete_configfile()
	{
		$dir = '/tftpboot/cm/';
		$file['1'] = $dir.'cm-'.$this->id.'.cfg';
		$file['2'] = $dir.'cm-'.$this->id.'.conf';

		foreach ($file as $f)
		{
			if (file_exists($f)) unlink($f);
		}
	}

	/**
	 * Restarts modem through snmpset
	 */
	public function restart_modem()
	{
		$config = ProvBase::first();
		$community_rw = $config->rw_community;
		$domain = $config->domain_name;

		// Log
		Log::info('restart modem '.$this->hostname);

		// if hostname cant be resolved we dont want to have an php error
		try
		{
			// restart modem - TODO: get community string and domain name from global config page, NOTE: OID from MIB: DOCS-CABLE-DEV-MIB::docsDevResetNow
			snmpset($this->hostname.'.'.$domain, $community_rw, "1.3.6.1.2.1.69.1.1.3.0", "i", "1", 300000, 1);
		}
		catch (Exception $e)
		{
			// only ignore error with this error message (catch exception with this string)
			if (((strpos($e->getMessage(), "php_network_getaddresses: getaddrinfo failed: Name or service not known") !== false) || (strpos($e->getMessage(), "snmpset(): No response from") !== false)))
			{
				// check if observer is called from HTML Update, otherwise skip
				if (\Request::method() == 'PUT')
				{
					// redirect back with corresponding message over flash, needs to be saved as it's normally only saved when the session middleware terminates successfully
					$resp = \Redirect::back()->with('message', 'Could not restart Modem! (offline/configfile error?)');
					\Session::driver()->save();		 // \ is like writing "use Session;" before class statement
					$resp->send();

					/*
					 * TODO: replace exit
					 * This is a security hassard. All Code (Observer etc) which should run after this code will not be executed !
					 */
					exit();
				}
			}
		}
	}


	/*
	 * Refresh Modem State
	 *
	 * This function will update the modem->status field with the modem upstream power level
	 * if online, otherwise it will set the value to 0.
	 *
	 * NOTE: This function will be called via artisan command modem-refresh. This command
	 *       is added to laravel scheduling api to refresh all modem states every 5min.
	 *
	 * NOTE: The function is written in a generic manner to fetch more than just upstream power level
	 *       For more see array $oids inside ..
	 *
	 * @param timeout: snmp timeout
	 * @return: result of snmpget as array of [oid1 => value1, ..]
	 * @author: Torsten Schmidt
	 */
	public function refresh_state ($timeout = 100*1000)
	{
		// Load Global Config
		$config = ProvBase::first();
		$community_ro = $config->ro_community;
		$domain = $config->domain_name;

		// Set SNMP default mode
		// TODO: use a seperate funciton
		snmp_set_quick_print(TRUE);
		snmp_set_oid_numeric_print(TRUE);
		snmp_set_valueretrieval(SNMP_VALUE_PLAIN);
		snmp_set_oid_output_format (SNMP_OID_OUTPUT_NUMERIC);

		// OID Array to parse
		// Style: ['modem table field 1' => 'oid1', 'modem table field 2' => 'oid2, ..']
		$oids = ['status' => '.1.3.6.1.2.1.10.127.1.2.2.1.3.2'];

		// Log
		// Log::debug('refresh state '.$this->hostname);

		$this->observer_disable();

		// if hostname cant be resolved we dont want to have an php error
		try
		{
			// SNMP request
			$session = new \SNMP(\SNMP::VERSION_2c, $this->hostname.'.'.$domain, $community_ro,  $timeout);
			$results = $r = $session->get($oids);

			// parse and update results
			foreach (array_reverse($oids) as $field => $oid)
				$this->{$field} = array_pop($r);

			// save
			$this->save();
		}
		catch (Exception $e)
		{
			// catch error
			// set fields to 0
			foreach (array_reverse($oids) as $field => $oid)
				$this->{$field} = 0;

			// save
			$this->save();

			return false;
		}

		return $results;
	}


	/*
	 * Return actual modem state as string or int
	 *
	 * @param return_type: ['string' (default), 'int']
	 * @return: string [ok, warning, critical, offline] or int [0 -> ok, 1 -> warning, 2 -> critical, 3 -> offline]
	 * @author: Torsten Schmidt
	 */
	public function get_state($return_type = 'string')
	{
		if ($this->status == 0)
			if ($return_type == 'string') return 'offline'; else return 3;

		if ($this->status > \Modules\HfcCustomer\Entities\ModemHelper::$single_critical_us)
			if ($return_type == 'string') return 'critical'; else return 2;

		if ($this->status > \Modules\HfcCustomer\Entities\ModemHelper::$single_warning_us)
			if ($return_type == 'string') return 'warning'; else return 1;

		if ($return_type == 'string') return 'ok'; else return 0;
	}


	/*
	 * Geocoding API
	 * Translate a address (like: Deutschland, Marienberg, Waldrand 4) in a geoposition (x,y)
	 *
	 * @author: Torsten Schmidt
	 *
	 * TODO: move to a seperate extensions class
	 */

	// private variable to hold the last Geocoding response state
	// use geocode_last_status()
	private $geocode_state = null;


	/*
	 * Modem Geocoding Function
	 * Geocode the modem address value in a geoposition and update values to x,y. Please
	 * note that the function is working in object context, so no addr parameters are required.
	 *
	 * @param save: Update Modem x,y value with a save() to DB. Notice this calls Observer !
	 * @return: true on success, false if coding fails. For error log see geocode_last_status()
	 * @author: Torsten Schmidt
	 *
	 * TODO: split in a general geocoding function and a modem specific one
	 */
	public function geocode ($save = true)
	{
		$country = 'Deutschland';

		// Load google key if .ENV is set
		$key = '';
		if (isset ($_ENV['GOOGLE_API_KEY']))
			$key = '&key='.$_ENV['GOOGLE_API_KEY'];

		// url encode the address
		$address = urlencode($country.', '.$this->street.', '.$this->zip.', '.$this->city);

		// google map geocode api url
		$url = "https://maps.google.com/maps/api/geocode/json?sensor=false&address={$address}$key";

		// get the json response
		$resp_json = file_get_contents($url);

		// Log
		Log::info ('geocode: request '.$url);

		// decode the json
		$resp = json_decode($resp_json, true);

		// response status will be 'OK', if able to geocode given address
		if($resp['status']=='OK')
		{
			// get the important data
			$lati = $resp['results'][0]['geometry']['location']['lat'];
			$longi = $resp['results'][0]['geometry']['location']['lng'];
			$formatted_address = $resp['results'][0]['formatted_address'];

			// verify if data is complete
			if($lati && $longi && $formatted_address)
			{
				// put the data in the array
				$data_arr = array();

				array_push(
					$data_arr,
					$lati,
					$longi
					// $formatted_address
					);

				$this->y = $lati;
				$this->x = $longi;
				$this->geocode_state = 'OK';

				if ($save)
					$this->save();

				Log::info('geocode: result '.$lati.','.$longi);

				return $data_arr;
			}
			else
			{
				$this->geocode_state = 'DATA_VERIFICATION_FAILED';
				Log::info('geocode: '.$this->geocode_state);
				return false;
			}
		}
		else
		{
			$this->geocode_state = $resp['status'];
			Log::info('geocode: '.$this->geocode_state);
			return false;
		}
	}


	/*
	 * Return Last Geocoding State / ERROR
	 */
	public function geocode_last_status ()
	{
		return $this->geocode_state;
	}


	/*
	 * Observer Handling
	 *
	 * To disable the update observers run observer_disable() on object context.
	 * This is useful to avoid running per modem observers on general (unimportant)
	 * changes. If obersers are enabled, every change on modem object will for example
	 * restart the modem.
	 */
	public $observer_enabled = true;

	public function observer_disable()
	{
		$this->observer_enabled = false;
	}
}


/**
 * Modem Observer Class
 * Handles changes on CMs
 *
 * can handle   'creating', 'created', 'updating', 'updated',
 *			  'deleting', 'deleted', 'saving', 'saved',
 *			  'restoring', 'restored',
 */
class ModemObserver
{
	public function created($modem)
	{
		$modem->hostname = 'cm-'.$modem->id;
		$modem->save();	 // forces to call the updating() and updated() method of the observer !
	}

	public function updating($modem)
	{
		if (!$modem->observer_enabled)
			return;

		// Use Updating to set the geopos before a save() is called.
		// Notice: that we can not call save() in update(). This will re-tricker
		//         the Observer and re-call update() -> endless loop is the result.
		$modem->geocode(false);

		// Refresh MPS rules
		// Note: does not perform a save() which could trigger observer.
		if (\BaseModel::__module_is_active('HfcCustomer'))
			$modem->tree_id = \Modules\Hfccustomer\Entities\Mpr::refresh($modem->id);
	}

	public function updated($modem)
	{
		if (!$modem->observer_enabled)
			return;

		$modem->make_dhcp_cm_all();
		$modem->make_configfile();
		$modem->restart_modem();
	}

	public function deleted($modem)
	{
		$modem->make_dhcp_cm_all();
		$modem->delete_configfile();
		$modem->restart_modem();
	}
}
