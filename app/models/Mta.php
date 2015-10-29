<?php

namespace Models;

class Mta extends \BaseModel {

    // The associated SQL table for this Model
    protected $table = 'mta';

	// Add your validation rules here
	public static function rules($id=null)
	{
		return array(
			'mac' => 'required|mac',
			'modem_id' => 'required|exists:modem,id|min:1',
			'configfile_id' => 'required|exists:configfile,id|min:1',
			/* 'hostname' => 'required|unique:mtas,hostname,'.$id, */
			'type' => 'required|exists:mta,type',
		);
	}

	// Don't forget to fill this array
	protected $fillable = ['mac', 'hostname', 'modem_id', 'configfile_id', 'type'];


    /**
     * Returns the data array needed for all views of the model
     */
	public function html_list_array ()
	{
		$ret = array (
				'configfiles' => $this->html_list($this->configfiles(), 'name'),
				'modems' => $this->html_list($this->modems(), 'hostname'),
				'mta_types' => Mta::getPossibleEnumValues('type', true)
			);
		return $ret;
	}

	/**
	 * return all modem objects
	 */
	private function modems()
	{
		return Modem::get();
	}


	/**
	 * return all Configfile Objects for MTAs
	 */
	private function configfiles()
	{
		return Configfile::where('device', '=', 'mta')->where('public', '=', 'yes')->get();
	}


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
     * BOOT:
     * - init mta observer
     */
    public static function boot()
    {
        parent::boot();

        Mta::observe(new MtaObserver);
    }

}

/**
 * MTA Observer Class
 * Handles changes on MTAs
 *
 * can handle   'creating', 'created', 'updating', 'updated',
 *              'deleting', 'deleted', 'saving', 'saved',
 *              'restoring', 'restored',
 */
class MTAObserver
{
    public function created($mta)
    {
        $mta->hostname = 'mta-'.$mta->id;
        $mta->save();
    }

    public function updating($mta)
    {
        $mta->hostname = 'mta-'.$mta->id;
    }
}
