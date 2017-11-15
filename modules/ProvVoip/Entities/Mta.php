<?php

namespace Modules\ProvVoip\Entities;

use File;
use Log;

use Modules\ProvBase\Entities\Modem;
use Modules\ProvBase\Entities\Configfile;
use Modules\ProvBase\Entities\ProvBase;

// Model not found? execute composer dump-autoload in nmsprime root dir
class Mta extends \BaseModel {

	// The associated SQL table for this Model
	public $table = 'mta';


	// Add your validation rules here
	public static function rules($id=null)
	{
		return array(
			'mac' => 'required|mac', //|unique:mta,mac',
			'modem_id' => 'required|exists:modem,id|min:1',
			'configfile_id' => 'required|exists:configfile,id|min:1',
			// 'hostname' => 'required|unique:mta,hostname,'.$id,
			'type' => 'required|exists:mta,type'
		);
	}

	/**
	 * View Stuff
	 */

	// Name of View
	public static function view_headline()
	{
		return 'MTAs';
	}

	// View Icon
	public static function view_icon()
	{
		return '<i class="fa fa-fax"></i>';
	}

	// link title in index view
	public function view_index_label()
	{
		$bsclass = $this->get_bsclass();
		$cf_name = 'No Configfile assigned';

		if (isset($this->configfile))
			$cf_name = $this->configfile->name;

		// TODO: use mta states.
		//       Maybe use fast ping to test if online in this function?

		return ['index' => [$this->hostname, $this->mac, $this->type, $cf_name],
				'index_header' => ['Name', 'MAC', 'Type', 'Configfile'],
				'bsclass' => $bsclass,
				'header' => $this->hostname.' - '.$this->mac];
	}

	// AJAX Index list function
	// generates datatable content and classes for model
	public function view_index_label_ajax()
	{
		$bsclass = $this->get_bsclass();

		return ['table' => $this->table,
				'index_header' => [$this->table.'.hostname', $this->table.'.mac', $this->table.'.type', 'configfile.name'],
				'header' => $this->hostname.' - '.$this->mac,
				'bsclass' => $bsclass,
				'order_by' => ['3' => 'asc'],
                'edit' => ['configfile.name' => 'has_configfile_assigned'],
				'eager_loading' => ['configfile']];
	}

	public function get_bsclass()
	{
		$bsclass = 'info';
		if (!isset($this->configfile))
			$bsclass = 'danger';

		return $bsclass;
	}

	public function has_configfile_assigned()
	{
		$cf_name = 'No Configfile assigned';

		if (isset($this->configfile))
			$cf_name = $this->configfile->name;

		return $cf_name;
	}



	public function view_belongs_to ()
	{
		return $this->modem;
	}

	public function view_has_many()
	{
		return array(
			'Phonenumber' => $this->phonenumbers,
		);
	}


	/**
	 * All Relations
	 */
	public function configfile()
	{
		return $this->belongsTo('Modules\ProvBase\Entities\Configfile', 'configfile_id');
	}

	public function modem()
	{
		return $this->belongsTo('Modules\ProvBase\Entities\Modem', 'modem_id');
	}

	public function phonenumbers()
	{
		return $this->hasMany('Modules\ProvVoip\Entities\Phonenumber');
	}


	// return all Configfile Objects for MTAs
	public function configfiles()
	{
		return Configfile::where('device', '=', 'mta')->where('public', '=', 'yes')->get();
	}


	/**
	 * Make Configfile for a single MTA
	 *
	 * @author Patrick Reichel
	 */
	public function make_configfile ()
	{
		$mta = $this;
		$id = $mta->id;
		$mac = $mta->mac;

		// dir; filenames
		$dir = '/tftpboot/mta/';
		$conf_file     = $dir."mta-$id.conf";
		$cfg_file      = $dir."mta-$id.cfg";

		// load configfile for mta
		$cf = $mta->configfile;

		if (!$cf)
		{
			Log::info("Error could not load configfile for mta ".$mta->id);
			goto _failed;
		}

		/*
		 * Write and Build configfile
		 * NOTE: We use docsis tool version 0.9.9 here where HASH building/adding is already implemented
		 * For Versions lower than 0.9.8 we have to build it twice and use european OID
		 * for pktcMtaDevProvConfigHash.0 from excentis packet cable mta mib
		 */
		$text = "Main\n{\n\tMtaConfigDelimiter 1;".$cf->text_make($mta, "mta")."\n\tMtaConfigDelimiter 255;\n}";
		if (!File::put($conf_file, $text))
		{
			Log::info('Error writing to file '.$conf_file_pre);
			goto _failed;
		}

		Log::info("/usr/local/bin/docsis -eu -p $conf_file $cfg_file");
		// "&" to start docsis process in background improves performance but we can't reliably proof if file exists anymore
		exec     ("/usr/local/bin/docsis -eu -p $conf_file $cfg_file >/dev/null 2>&1 &", $out);

		// this only is valid when we dont execute docsis in background
		// if (!file_exists($cfg_file))
		// {
		// 	Log::info('Error failed to build '.$cfg_file);
		// 	goto _failed;
		// }


		// change owner in case command was called from command line via php artisan nms:configfile that changes owner to root
		system('/bin/chown -R apache /tftpboot/mta');
		return true;

_failed:
		// change owner in case command was called from command line via php artisan nms:configfile that changes owner to root
		system('/bin/chown -R apache /tftpboot/mta');
		return false;
	}

	/**
	 * Make configfiles for all MTAs
	 *
	 * @author Patrick Reichel
	 */
	public function make_configfile_all()
	{
		$mtas = Mta::all();
		foreach ($mtas as $mta)
		{
			if ($mta->id == 0)
				continue;
			if (!$mta->make_configfile())
				Log::warning("failed to build/write configfile for mta mta-".$mta->id);
		}

		return true;
	}


	/**
	 * BOOT:
	 * - init mta observer
	 */
	public static function boot()
	{
		parent::boot();

		Mta::observe(new MtaObserver);
		Mta::observe(new \App\SystemdObserver);
	}


	/**
	 * Define DHCP Config File for MTA's
	 */
	const CONF_FILE_PATH = '/etc/dhcp/nmsprime/mta.conf';

	/**
	 * Writes all mta entries to dhcp configfile
	 */
	public static function make_dhcp_mta_all()
	{
		Mta::clear_dhcp_conf_file();

		$data = '';

		foreach (Mta::all() as $mta)
		{
			if ($mta->id == 0)
				continue;

			$data .= 'host mta-'.$mta->id.' { hardware ethernet '.$mta->mac.'; filename "mta/mta-'.$mta->id.'.cfg"; ddns-hostname "mta-'.$mta->id.'"; option host-name "'.$mta->id.'"; }'."\n";
		}

		File::put(self::CONF_FILE_PATH, $data);
		return true;
	}


	/**
	 * Create/Update/Delete single Entry in mta dhcpd configfile
	 * See Modem@make_dhcp_cm for more explanations
	 *
	 * @author Nino Ryschawy
	 */
	public function make_dhcp_mta($delete = false)
	{
		Log::debug(__METHOD__." started");

		$orig = $this->getOriginal();

		if (!file_exists(self::CONF_FILE_PATH))
		{
			Log::critical('Missing DHCPD Configfile '.self::CONF_FILE_PATH);
			return;
		}

		// lock
		$fp = fopen(self::CONF_FILE_PATH, "r+");

		if (!flock($fp, LOCK_EX))
			Log::error('Could not get exclusive lock for '.self::CONF_FILE_PATH);


		$replace = $orig ? $orig['mac'] : $this->mac;
		// $conf = File::get(self::CONF_FILE_PATH);
		$conf = file(self::CONF_FILE_PATH);

		foreach ($conf as $key => $line)
		{
			if (strpos($line, $replace))
			{
				unset($conf[$key]);
				break;
			}
		}
		// dont replace directly as this wouldnt add the entry for a new created mta
		// $conf = str_replace($replace, '', $conf);
		if (!$delete)
		{
			$data 	= 'host mta-'.$this->id.' { hardware ethernet '.$this->mac.'; filename "mta/mta-'.$this->id.'.cfg"; ddns-hostname "mta-'.$this->id.'"; option host-name "'.$this->id.'"; }'."\n";
			$conf[] = $data;
		}

		Modem::_write_dhcp_file(self::CONF_FILE_PATH, implode($conf));

		// unlock
		flock($fp, LOCK_UN); fclose($fp);
	}


	/**
	 * Deletes the configfiles with all mta dhcp entries - used to refresh the config through artisan nms:dhcp command
	 */
	public static function clear_dhcp_conf_file()
	{
		File::put(self::CONF_FILE_PATH, '');
	}


	/**
	 * Deletes Configfile of one mta
	 */
	public function delete_configfile()
	{
		$dir = '/tftpboot/mta/';
		$file['1'] = $dir.'mta-'.$this->id.'.cfg';
		$file['2'] = $dir.'mta-'.$this->id.'.conf';

		foreach ($file as $f)
		{
			if (file_exists($f)) unlink($f);
		}
	}


	/**
	 * Restarts MTA through snmpset
	 */
	public function restart()
	{
		// Log
		Log::info('restart MTA '.$this->hostname);

		// if hostname cant be resolved we dont want to have an php error
		try
		{
			$domain = ProvVoip::first()->mta_domain;

			if (!$domain)
				$domain = ProvBase::first()->domain_name;

			$fqdn = $this->hostname.'.'.$domain;

			// restart - PKTC-EXCENTIS-MTA-MIB::pktcMtaDevResetNow
			// NOTES: Version 2 is important!
			// 'private' is the always working default community
			snmp2_set($fqdn, 'private', '1.3.6.1.4.1.7432.1.1.1.1.0', 'i', '1', 300000, 1);
		}
		catch (\Exception $e)
		{
			// only ignore error with this error message (catch exception with this string)
			if (((strpos($e->getMessage(), "php_network_getaddresses: getaddrinfo failed: Name or service not known") !== false) || (strpos($e->getMessage(), "snmp2_set(): No response from") !== false))) {
				\Session::flash('error', 'Could not restart MTA! (offline?)');
			}
			elseif(strpos($e->getMessage(), "noSuchName") !== false) {
				// this is not necessarily an error, e.g. the modem was deleted (i.e. Cisco) and user clicked on restart again
			}
			else {
				// Inform and log for all other exceptions
				\Session::push('tmp_error_above_form', 'Unexpected exception: '.$e->getMessage());
				\Log::error("Unexpected exception restarting MTA ".$this->id." (".$this->mac."): ".$e->getMessage()." => ".$e->getTraceAsString());
				\Session::flash('error', '');
			}
		}

	}
}


/**
 * MTA Observer Class
 * Handles changes on MTAs
 *
 * can handle   'creating', 'created', 'updating', 'updated',
 *              'deleting', 'deleted', 'saving', 'saved',
 *              'restoring', 'restored',
 *
 * @author Patrick Reichel
 */
class MtaObserver
{
	public function created($mta)
	{
		$mta->hostname = 'mta-'.$mta->id;
		$mta->save(); 			// forces to call updated method
		$mta->modem->make_dhcp_cm(false, true);
		$mta->modem->restart_modem();
	}

	public function updated($mta)
	{
		$modifications = $mta->getDirty();
		if (isset($modifications['updated_at']))
			unset($modifications['updated_at']);

		// only make configuration files when relevant data was changed
		if ($modifications)
		{
			if (array_key_exists('mac', $modifications)){
				$mta->make_dhcp_mta();
				$mta->modem->make_configfile();
			}

			$mta->make_configfile();
		}

		$mta->restart();
	}

	public function deleted($mta)
	{
		$mta->make_dhcp_mta(true);
		$mta->modem->make_dhcp_cm(false, true);
		$mta->delete_configfile();
		$mta->modem->make_configfile();
		$mta->modem->restart_modem();
	}
}
