<?php

namespace Modules\ProvVoip\Entities;

use File;
use Log;

use Modules\ProvBase\Entities\Modem;
use Modules\ProvBase\Entities\Configfile;

// Model not found? execute composer dump-autoload in lara root dir
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


	// Name of View
	public static function view_headline()
	{
		return 'MTAs';
	}

    // link title in index view
    public function view_index_label()
    {
        $bsclass = 'info';

        // TODO: use mta states.
        //       Maybe use fast ping to test if online in this function?

        return ['index' => [$this->hostname, $this->mac, $this->type, $this->configfile->name],
                'index_header' => ['Name', 'MAC', 'Type', 'Configfile'],
                'bsclass' => $bsclass,
                'header' => $this->hostname.' - '.$this->mac];
    }


	/**
	 * return all modem objects
	 */
	public function modems()
	{
		return Modem::get();
	}

	/**
	 * return all Configfile Objects for MTAs
	 */
	public function configfiles()
	{
		return Configfile::where('device', '=', 'mta')->where('public', '=', 'yes')->get();
	}


	/**
	 * All Relations
	 *
	 * link with configfiles
	 */
	public function configfile()
	{
		return $this->belongsTo('Modules\ProvBase\Entities\Configfile', 'configfile_id');
	}

	/**
	 * link with modems
	 */
	public function modem()
	{
		return $this->belongsTo('Modules\ProvBase\Entities\Modem', 'modem_id');
	}

	/**
	 * link with phonenumbers
	 */
	public function phonenumbers()
	{
		return $this->hasMany('Modules\ProvVoip\Entities\Phonenumber');
	}

	// belongs to a modem - see BaseModel for explanation
	public function view_belongs_to ()
	{
		return $this->modem;
	}

	// returns all objects that are related to a mta
	public function view_has_many()
	{
		return array(
			'Phonenumber' => $this->phonenumbers,
		);
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
		$hostname = $mta->hostname;

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
	const CONF_FILE_PATH = '/etc/dhcp/nms/mta.conf';

	/**
	 * Writes all mta entries to dhcp configfile
	 */
	public function make_dhcp_mta_all()
	{
		$mtas = Mta::all();
		$data = '';

		foreach ($mtas as $mta)
		{
			if ($mta->id == 0)
				continue;

			$data .= 'host mta-'.$mta->id.' { hardware ethernet '.$mta->mac.'; filename "mta/mta-'.$mta->id.'.cfg"; ddns-hostname "mta-'.$mta->id.'"; option host-name "'.$mta->id.'"; }'."\n";
		}

		File::put(self::CONF_FILE_PATH, $data);
		return true;
	}

    /**
     * Deletes the configfiles with all mta dhcp entries - used to refresh the config through artisan nms:dhcp command
     */
	public function del_dhcp_conf_file()
	{
        if (file_exists(self::CONF_FILE_PATH)) unlink(self::CONF_FILE_PATH);
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
        $mta->make_configfile();
        $mta->make_dhcp_mta_all();
		$mta->save();
	}

	public function updating($mta)
	{
		$mta->hostname = 'mta-'.$mta->id;
	}

	public function updated($mta)
	{
        $mta->make_dhcp_mta_all();
		$mta->make_configfile();
		$mta->modem->restart_modem();
	}

	public function deleted($mta)
	{
        $mta->make_dhcp_mta_all();
		$mta->delete_configfile();
		$mta->modem->restart_modem();
	}
}
