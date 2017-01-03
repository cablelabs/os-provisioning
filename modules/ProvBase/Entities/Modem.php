<?php

namespace Modules\ProvBase\Entities;

use File;
use Log;
use Exception;
use Modules\ProvBase\Entities\Qos;
use Modules\ProvBase\Entities\ProvBase;

class Modem extends \BaseModel {

	// get functions for some address select options
	use \App\Models\AddressFunctionsTrait;

	// The associated SQL table for this Model
	public $table = 'modem';


	// Add your validation rules here
	// see: http://stackoverflow.com/questions/22405762/laravel-update-model-with-unique-validation-rule-for-attribute
	public static function rules($id = null)
	{
		return array(
			'mac' => 'required|mac|unique:modem,mac,'.$id.',id,deleted_at,NULL',
			'birthday' => 'date',
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
			case 2: $bsclass = 'warning'; break; // critical
			case 3: $bsclass = 'danger'; $status = 'offline'; break; // offline

			default: $bsclass = 'danger'; break;
		}

		return ['index' => [$this->id, $this->mac, $this->name, $this->lastname, $this->city, $this->street, $status],
		        'index_header' => ['Modem Number', 'MAC address', 'Name', 'Lastname', 'City', 'Street', 'US level'],
		        'bsclass' => $bsclass,
		        'header' => $this->id.' - '.$this->mac.($this->name ? ' - '.$this->name : '')];
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

	/**
	 * Get relation to envia orders.
	 *
	 * @author Patrick Reichel
	 */
	protected function _envia_orders() {

		if (!\PPModule::is_active('provvoipenvia')) {
			throw new \LogicException(__METHOD__.' only callable if module ProvVoipEnvia as active');
		}

		return $this->hasMany('Modules\ProvVoipEnvia\Entities\EnviaOrder')->where('ordertype', 'NOT LIKE', 'order/create_attachment');

	}

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
		if (\PPModule::is_active('ProvVoip'))
			return $this->hasMany('Modules\ProvVoip\Entities\Mta');

		return null;
	}

	public function tree()
	{
		if (\PPModule::is_active('HfcBase'))
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

		$ret = array();

		// we use a dummy here as this will be overwritten by ModemController::get_form_tabs()
		if (\PPModule::is_active('ProvVoip')) {
			$ret['dummy']['Mta']['class'] = 'Mta';
			$ret['dummy']['Mta']['relation'] = $this->mtas;
		}

		if (\PPModule::is_active('provvoipenvia'))
		{
			$ret['dummy']['EnviaOrder']['class'] = 'EnviaOrder';
			$ret['dummy']['EnviaOrder']['relation'] = $this->_envia_orders;

			// TODO: auth - loading controller from model could be a security issue ?
			$ret['dummy']['Envia API']['view']['view'] = 'provvoipenvia::ProvVoipEnvia.actions';
			$ret['dummy']['Envia API']['view']['vars']['extra_data'] = \Modules\ProvBase\Http\Controllers\ModemController::_get_envia_management_jobs($this);
		}

		return $ret;
	}


	/**
	 * BOOT:
	 * - init modem observer
	 */
	public static function boot()
	{
		parent::boot();

		Modem::observe(new \App\SystemdObserver);
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
	private function generate_cm_dhcp_entry()
	{
		$ret = 'host cm-'.$this->id.' { hardware ethernet '.$this->mac.'; filename "cm/cm-'.$this->id.'.cfg"; ddns-hostname "cm-'.$this->id.'";';

		if(count($this->mtas))
			$ret .= ' option ccc.dhcp-server-1 '.ProvBase::first()->provisioning_server.';';

		$ret .= "}\n";
		return $ret;
	}

	private function generate_cm_dhcp_entry_pub()
	{
			return 'subclass "Client-Public" '.$this->mac.'; # CM id:'.$this->id."\n";
	}


	/**
	 * Deletes the configfiles with all modem dhcp entries - used to refresh the config through artisan nms:dhcp command
	 *
	 * @author Nino Ryschawy
	 */
	public static function clear_dhcp_conf_files()
	{
		File::put(self::CONF_FILE_PATH, '');
		File::put(self::CONF_FILE_PATH_PUB, '');
	}



	/**
	 * Make DHCP config files for all CMs including EPs - used in dhcpCommand after deleting
	 * the config files with all entries
	 *
	 * TODO: make static function (class context not object)
	 *
	 * @author Torsten Schmidt
	 */
	public static function make_dhcp_cm_all ()
	{
		Modem::clear_dhcp_conf_files();

		// Log
		Log::info('dhcp: update '.self::CONF_FILE_PATH.', '.self::CONF_FILE_PATH_PUB);

		$data     = '';
		$data_pub = '';

		foreach (Modem::all() as $modem)
		{
			if ($modem->id == 0)
				continue;

			// all
			$data .= $modem->generate_cm_dhcp_entry();

			// public ip
			if ($modem->public)
				$data_pub .= $modem->generate_cm_dhcp_entry_pub();

		}

		$ret = File::put(self::CONF_FILE_PATH, $data);
		if ($ret === false)
			die("Error writing to file");

		$ret = File::append(self::CONF_FILE_PATH_PUB, $data_pub);
		if ($ret === false)
			die("Error writing to file");


		// chown for future writes in case this function was called from CLI via php artisan nms:dhcp that changes owner to 'root'
		system('/bin/chown -R apache /etc/dhcp/');
	}


	/**
	 * Make Configfile for a single CM
	 */
	public function make_configfile ()
	{
		$modem	= $this;
		$id		= $modem->id;
		$mac	= $modem->mac;
		$host	= $modem->hostname;

		/* Configfile */
		$dir		= '/tftpboot/cm/';
		$cf_file	= $dir."cm-$id.conf";
		$cfg_file	= $dir."cm-$id.cfg";

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
			// restart modem - NOTE: OID from MIB: DOCS-CABLE-DEV-MIB::docsDevResetNow
			snmpset($this->hostname.'.'.$domain, $community_rw, "1.3.6.1.2.1.69.1.1.3.0", "i", "1", 300000, 1);
		}
		catch (Exception $e)
		{
			// only ignore error with this error message (catch exception with this string)
			if (((strpos($e->getMessage(), "php_network_getaddresses: getaddrinfo failed: Name or service not known") !== false) || (strpos($e->getMessage(), "snmpset(): No response from") !== false))) {
				\Session::flash('error', 'Could not restart Modem! (offline?)');
			}
			else {
				// re-throw all other exceptions
				throw $e;
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
		\Log::debug('Refresh Modem State', [$this->hostname]);

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

		$this->observer_disable();

		// if hostname cant be resolved we dont want to have an php error
		try
		{
			// SNMP request
			$session = new \SNMP(\SNMP::VERSION_2c, $this->hostname.'.'.$domain, $community_ro,  $timeout);
			$results = $r = $session->get($oids);

			// parse and update results
			foreach (array_reverse($oids) as $field => $oid)
				$this->{$field} = array_pop($r) / 10;	// TODO: added generic concept for multiplying options @Torsten Schmidt

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

	/**
	 * Refresh Modem State using cached value of Cacti
	 *
	 * This function will update the modem->status field with the modem upstream power level
	 * if online. Because the last value of Cacti is used, the update is much quicker and doesn't
	 * generate a superfluous SNMP request.
	 *
	 * NOTE: This function will be called via artisan command modem-refresh. This command
	 *       is added to laravel scheduling api to refresh all modem states every 5min.
	 *
	 * @return: maximum power level of all upstream channels or -1 on error
	 * @author: Ole Ernst
	 */
	public function refresh_state_cacti()
	{
		// cacti is not installed
		if(!\PPModule::is_active('provmon'))
			return -1;

		try {
			$path = \DB::connection('mysql-cacti')->table('host')
				->join('data_local', 'host.id', '=', 'data_local.host_id')
				->join('data_template_data', 'data_local.id', '=', 'data_template_data.local_data_id')
				->where('host.description', '=', $this->hostname)
				->orderBy('data_local.id')
				->select('data_template_data.data_source_path')->first();
		}
		catch (\PDOException $e) {
			// Code 1049 == Unknown database '%s' -> cacti is not installed yet
			if($e->getCode() == 1049)
				return -1;
			// don't catch other PDOExceptions
			throw $e;
		}

		// no rrd file for current modem found in DB
		if(!$path)
			return -1;

		$file = str_replace('<path_rra>', '/usr/share/cacti/rra', $path->data_source_path);
		// file does not exist
		if(!File::exists($file))
			return -1;

		$output = array();
		exec("rrdtool lastupdate $file", $output);
		// unexpected number of lines from rrdtool
		if(count($output) != 3)
			return -1;

		$keys = explode(' ', trim(array_shift($output)));
		$vals = explode(' ', trim(explode(':', array_pop($output))[1]));
		$res = array_combine($keys, $vals)['maxUsPow'];

		$status = \DB::connection('mysql-cacti')->table('host')
			->where('description', '=', $this->hostname)
			->select('status')->first()->status;
		// modem is offline, if we use last value of cacti instead of setting it
		// to zero, it would seem as if the modem is still online
		if($status == 1)
			$res = 0;

		$this->observer_disable();
		$this->status = $res;
		$this->save();

		return $res;
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
		$address = urlencode($country.', '.$this->street.' '.$this->house_number.', '.$this->zip.', '.$this->city);

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
	public function observer_disable()
	{
		$this->observer_enabled = false;
	}
}


/**
 * Modem Observer Class
 * Handles changes on CMs, can handle:
 *
 * 'creating', 'created', 'updating', 'updated',
 * 'deleting', 'deleted', 'saving', 'saved',
 * 'restoring', 'restored',
 */
class ModemObserver
{
	public function created($modem)
	{
		if (\PPModule::is_active ('ProvMon'))
			\Artisan::call('nms:cacti', ['--cmts-id' => 0, '--modem-id' => $modem->id]);
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
		if (\PPModule::is_active('HfcCustomer'))
			$modem->tree_id = \Modules\HfcCustomer\Entities\Mpr::refresh($modem->id);
	}

	public function updated($modem)
	{
		if (!$modem->observer_enabled)
			return;

		// only restart on system relevant changes ? Then it's not that easy to restart modem anymore
		$modem->restart_modem();
		$modem->make_dhcp_cm_all();
		$modem->make_configfile();
	}

	public function deleted($modem)
	{
		$modem->restart_modem();
		$modem->make_dhcp_cm_all();
		$modem->delete_configfile();
	}
}
