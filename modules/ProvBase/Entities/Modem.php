<?php

namespace Modules\ProvBase\Entities;

use File;
use Log;
use Exception;
use Modules\ProvBase\Entities\Qos;
use Modules\ProvBase\Entities\ProvBase;
use Modules\ProvMon\Http\Controllers\ProvMonController;

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

	// View Icon
	public static function view_icon()
	{
		return '<i class="fa fa-hdd-o"></i>';
	}

	// link title in index view
	public function view_index_label()
	{
		$bsclass = 'success';
		$us_pwr = $this->us_pwr.' dBmV';

		switch ($this->get_state('int'))
		{
			case 0:	$bsclass = 'success'; break; // online
			case 1: $bsclass = 'warning'; break; // warning
			case 2: $bsclass = 'warning'; break; // critical
			case 3: $bsclass = 'danger'; $us_pwr = 'offline'; break; // offline

			default: $bsclass = 'danger'; break;
		}

		$configfile = $this->configfile ? $this->configfile->name : '';

		return ['index' => [$this->id, $this->mac, $configfile, $this->name, $this->lastname, $this->city, $this->street, $us_pwr],
		        'index_header' => ['Modem Number', 'MAC address', 'Configfile', 'Name', 'Lastname', 'City', 'Street', 'US level'],
		        'bsclass' => $bsclass,
		        'header' => $this->id.' - '.$this->mac.($this->name ? ' - '.$this->name : '')];
	}

	public function index_list()
	{
		return $this->orderBy('id', 'desc')->with('configfile')->get();
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

	// TODO: rename to device - search for all places where this function is used
	public function tree()
	{
		if (\PPModule::is_active('HfcReq'))
			return $this->belongsTo('Modules\HfcReq\Entities\NetElement');

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
		// Log
		Log::info('restart modem '.$this->hostname);

		// if hostname cant be resolved we dont want to have an php error
		try
		{
			$config = ProvBase::first();
			$fqdn = $this->hostname.'.'.$config->domain_name;
			$cmts = ProvMonController::get_cmts(gethostbyname($fqdn));
			$mac_oid = implode('.', array_map('hexdec', explode(':', $this->mac)));

			if($cmts && $cmts->company == 'Cisco') {
				// delete modem entry in cmts - CISCO-DOCS-EXT-MIB::cdxCmCpeDeleteNow
				snmpset($cmts->ip, $cmts->get_rw_community(), '1.3.6.1.4.1.9.9.116.1.3.1.1.9.'.$mac_oid, 'i', '1', 300000, 1);
			}
			elseif($cmts && $cmts->company == 'Casa') {
				// reset modem via cmts, deleting is not possible - CASA-CABLE-CMCPE-MIB::casaCmtsCmCpeResetNow
				snmpset($cmts->ip, $cmts->get_rw_community(), '1.3.6.1.4.1.20858.10.12.1.3.1.7.'.$mac_oid, 'i', '1', 300000, 1);
			}
			else {
				// restart modem - DOCS-CABLE-DEV-MIB::docsDevResetNow
				snmpset($fqdn, $config->rw_community, '1.3.6.1.2.1.69.1.1.3.0', 'i', '1', 300000, 1);
			}
		}
		catch (Exception $e)
		{
			// only ignore error with this error message (catch exception with this string)
			if (((strpos($e->getMessage(), "php_network_getaddresses: getaddrinfo failed: Name or service not known") !== false) || (strpos($e->getMessage(), "snmpset(): No response from") !== false))) {
				\Session::flash('error', 'Could not restart Modem! (offline?)');
			}
			elseif(strpos($e->getMessage(), "noSuchName") !== false) {
				// this is not necessarily an error, e.g. the modem was deleted (i.e. Cisco) and user clicked on restart again
			}
			else {
				// Inform and log for all other exceptions
				\Session::push('tmp_info_above_form', 'Unexpected exception: '.$e->getMessage());
				\Log::error("Unexpected exception restarting modem ".$this->id." (".$this->mac."): ".$e->getMessage()." => ".$e->getTraceAsString());
				\Session::flash('error', '');
			}
		}

	}


	/*
	 * Refresh Modem State
	 *
	 * This function will update the modem upstream/downstream power level/SNR
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
		$oids = ['us_pwr' => '.1.3.6.1.2.1.10.127.1.2.2.1.3.2',
				 /*'us_snr' => 'not possible using modem oids',*/
				 'ds_pwr' => '.1.3.6.1.2.1.10.127.1.1.1.1.6',
				 'ds_snr' => '.1.3.6.1.2.1.10.127.1.1.4.1.5',
				 ];

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
	 * This function will update the upstream/downstream power level/SNR
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
		$arr = array_combine($keys, $vals);

		$res = ['us_pwr' => $arr['avgUsPow'],
				'us_snr' => $arr['avgUsSNR'],
				'ds_pwr' => $arr['avgDsPow'],
				'ds_snr' => $arr['avgDsSNR'],
				];

		$status = \DB::connection('mysql-cacti')->table('host')
			->where('description', '=', $this->hostname)
			->select('status')->first()->status;
		// modem is offline, if we use last value of cacti instead of setting it
		// to zero, it would seem as if the modem is still online
		if($status == 1)
			array_walk($res, function(&$val) {$val = 0;} );

		$this->observer_disable();
		foreach($res as $key => $val)
			$this->{$key} = round($val);
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
		if ($this->us_pwr == 0)
			if ($return_type == 'string') return 'offline'; else return 3;

		if ($this->us_pwr > \Modules\HfcCustomer\Entities\ModemHelper::$single_critical_us)
			if ($return_type == 'string') return 'critical'; else return 2;

		if ($this->us_pwr > \Modules\HfcCustomer\Entities\ModemHelper::$single_warning_us)
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


	/**
	 * Check if modem has phonenumbers attached
	 *
	 * @author Patrick Reichel
	 *
	 * @return True if phonenumbers attached to one of the modem's MTA, else False
	 */
	public function has_phonenumbers_attached() {

		// if there is no voip module ⇒ there can be no numbers
		if (!\PPModule::is_active('provvoip')) {
			return False;
		}

		foreach ($this->mtas as $mta) {
			foreach ($mta->phonenumbers->all() as $phonenumber) {
				return True;
			}
		}

		// no numbers found
		return False;


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


	/**
	 * Before deleting a modem and all children we have to check some things
	 *
	 * @author Patrick Reichel
	 */
	public function delete() {

		// deletion of modems with attached phonenumbers is not allowed with enabled Envia module
		// prevent user from (recursive and implicite) deletion of phonenumbers before termination at Envia!!
		// we have to check this here as using ModemObserver::deleting() with return false does not prevent the monster from deleting child model instances!
		if (\PPModule::is_active('ProvVoipEnvia')) {
			if ($this->has_phonenumbers_attached()) {

				// check from where the deletion request has been triggered and set the correct var to show information
				$prev = explode('?', \URL::previous())[0];
				$prev = \Str::lower($prev);
				$msg = "You are not allowed to delete a modem with attached phonenumbers!";
				if (\Str::endsWith($prev, 'edit')) {
					\Session::push('tmp_info_above_relations', $msg);
				}
				elseif (\Str::endsWith($prev, 'modem')) {
					\Session::push('tmp_info_above_index_list', $msg);
				}

				return false;
			}
		}

		// when arriving here: start the standard deletion procedure
		return parent::delete();
	}

	/**
	 * Calculates the great-circle distance between this and $modem, with
	 * the Haversine formula.
	 *
	 * see https://stackoverflow.com/questions/514673/how-do-i-open-a-file-from-line-x-to-line-y-in-php#tab-top
	 *
	 * @return float Distance between points in [m] (same as earthRadius)
	 */

	private function _haversine_great_circle_distance($modem)
	{
		// convert from degrees to radians
		$latFrom = deg2rad($this->y);
		$lonFrom = deg2rad($this->x);
		$latTo = deg2rad($modem->y);
		$lonTo = deg2rad($modem->x);

		$latDelta = $latTo - $latFrom;
		$lonDelta = $lonTo - $lonFrom;

		$angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) + cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

		// earth radius
		return $angle * 6371000;
	}

	/**
	 * Clean modem from all Envia related data – call this e.g. if you delete the last number from this modem.
	 * We have to do this to avoid problems in case we want to install this modem at another customer
	 *
	 * @author Patrick Reichel
	 */
	public function remove_envia_related_data() {

		// first: check if envia module is enabled
		// if not: do nothing – this database fields could be in use by another voip provider module!
		if (\PPModule::is_active('ProvVoipEnvia')) {
			return;
		}

		$this->contract_external_id = NULL;
		$this->contract_ext_creation_date = NULL;
		$this->contract_ext_termination_date = NULL;
		$this->installation_address_change_date = NULL;
		$this->save();


	}

	public function proximity_search($radius) {
		$ids = 'id = 0';
		foreach (Modem::all() as $modem)
			if ($this->_haversine_great_circle_distance($modem) < $radius)
				$ids .= " OR id = $modem->id";
		return $ids;
	}

	/**
	 * Check if modem actually needs to be restarted. This is only the case if a
	 * relevant attribute was modified.
	 *
	 * @author Ole Ernst
	 */
	public function needs_restart() {
		$diff = array_diff_assoc($this->getAttributes(), $this->getOriginal());

		return array_key_exists('contract_id', $diff)
			|| array_key_exists('mac', $diff)
			|| array_key_exists('public', $diff)
			|| array_key_exists('network_access', $diff)
			|| array_key_exists('configfile_id', $diff)
			|| array_key_exists('qos_id', $diff);
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
		$modem->hostname = 'cm-'.$modem->id;
		$modem->save();	 // forces to call the updating() and updated() method of the observer !
		if (\PPModule::is_active ('ProvMon'))
			\Artisan::call('nms:cacti', ['--cmts-id' => 0, '--modem-id' => $modem->id]);
	}

	public function updating($modem)
	{

		// reminder: on active Envia module: moving modem with phonenumbers attached to other contract is not allowed!
		// check if this is running if you decide to implement moving of modems to other contracts
		// watch Ticket LAR-106
		if (\PPModule::is_active('ProvVoipEnvia')) {
			if (
				// updating is also called on create – so we have to check this
				(!$modem->wasRecentlyCreated)
				&&
				($modem['original']['contract_id'] != $modem->contract_id)
			) {
				if ($modem->has_phonenumbers_attached) {
					// returning false should cancel the updating: verify this! There has been some problems with deleting modems – we had to put the logic in Modem::delete() probably caused by our Base* classes…
					// see: http://laravel-tricks.com/tricks/cancelling-a-model-save-update-delete-through-events
					return false;
				}
				elseif ($modem->contract_external_id) {
					// if there are any Envia data: the number(s) are probably moved only temporary
					// here we have to think about the references to this modem in all related EnviaOrders (maybe all the numbers have been terminated and then deleted from our database – but we still have the orders related to this numbers and also to this modem
					return false;
				}
			}
		}

		if (!$modem->observer_enabled)
			return;

		// Use Updating to set the geopos before a save() is called.
		// Notice: that we can not call save() in update(). This will re-tricker
		//         the Observer and re-call update() -> endless loop is the result.
		$modem->geocode(false);

		// Refresh MPS rules
		// Note: does not perform a save() which could trigger observer.
		if (\PPModule::is_active('HfcCustomer'))
			$modem->netelement_id = \Modules\HfcCustomer\Entities\Mpr::refresh($modem->id);
	}

	public function updated($modem)
	{
		if (!$modem->observer_enabled)
			return;

		if($modem->needs_restart() || \Input::has('_force_restart'))
			$modem->restart_modem();
		$modem->make_dhcp_cm_all();
		$modem->make_configfile();

		// ATTENTION:
		// If we ever think about moving modems to other contracts we have to delete Envia related stuff, too –
		// check contract_ext* and installation_address_change_date
		// moving then should only be allowed without attached phonenumbers and terminated Envia contract!
		// cleaner in Patrick's opinion would be to delete and re-create the modem
	}

	public function deleted($modem)
	{
		$modem->restart_modem();
		$modem->make_dhcp_cm_all();
		$modem->delete_configfile();
	}
}
