<?php

namespace Models;

use File;
use Log;

// Model not found? execute composer dump-autoload in lara root dir
class Mta extends \BaseModel {

	// for soft deleting => move to BaseModel?
	use \Illuminate\Database\Eloquent\SoftDeletingTrait;
	protected $dates = ['deleted_at'];

	// Add your validation rules here
	public static function rules($id=null)
	{
		return array(
			'mac' => 'required|mac',
			'modem_id' => 'required|exists:modems,id|min:1',
			'configfile_id' => 'required|exists:configfiles,id|min:1',
			/* 'hostname' => 'required|unique:mtas,hostname,'.$id, */
			'type' => 'required|exists:mtas,type',
		);
	}

	// Don't forget to fill this array
	protected $fillable = ['mac', 'hostname', 'modem_id', 'configfile_id', 'type'];


	/**
	 * link with configfiles
	 */
	public function configfile()
	{
		return $this->belongsTo('Models\Configfile');
	}

	/**
	 * link with modems
	 */
	public function modem()
	{
		return $this->belongsTo('Models\Modem');
	}

	/**
	 * link with phonenumbers
	 */
	public function phonenumbers()
	{
		return $this->hasMany('Models\Phonenumber');
	}

	/**
	 * BOOT:
	 * - init mta observer
	 */
	public static function boot()
	{
		parent::boot();

		Mta::observe(new MtaObserver);
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

		Log::info("/usr/local/bin/docsis -e $cf_file $dir/../keyfile $dir/mta-$id.cfg");
		exec("/usr/local/bin/docsis -e $cf_file $dir/../keyfile $dir/mta-$id.cfg", $out, $ret);

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
class MTAObserver
{
	public function created($mta)
	{
		$mta->hostname = 'mta-'.$mta->id;
        $mta->make_configfile();
		$mta->save();
	}

	public function updating($mta)
	{
		$mta->hostname = 'mta-'.$mta->id;
	}

	public function updated($mta)
	{
        $mta->make_configfile();
	}
}
