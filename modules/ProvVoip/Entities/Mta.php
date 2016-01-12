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
	public static function get_view_header()
	{
		return 'MTAs';
	}

	// link title in index view
	public function get_view_link_title()
	{
		return $this->hostname;
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

	// returns all objects that are related to a cmts
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

		// Configfile
		$dir = '/tftpboot/mta/';
		$cf_file = $dir."mta-$id.conf";

		$cf = $mta->configfile;

		if (!$cf)
			return false;

		$text = "Main\n{\n\t".$cf->text_make($mta, "mta")."\n}";
		$ret  = File::put($cf_file, $text);

		if ($ret === false)
			die("Error writing to file ".$dir.$cf_file);

		Log::info("/usr/local/bin/docsis -p $cf_file $dir/mta-$id.cfg");
		exec("/usr/local/bin/docsis -p $cf_file $dir/mta-$id.cfg >/dev/null 2>&1 &", $out, $ret);

		// change owner in case command was called from command line via php artisan nms:configfile that changes owner to root
		system('/bin/chown -R apache /tftpboot/mta');

		return ($ret == 0 ? true : false);
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
		Mta::observe(new \SystemdObserver);
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
	}

	public function deleted($mta)
	{
        $mta->make_dhcp_mta_all();
		$mta->delete_configfile();

	}
}
