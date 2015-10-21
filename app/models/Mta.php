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
