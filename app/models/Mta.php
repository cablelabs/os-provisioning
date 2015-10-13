<?php

namespace Models;

class Mta extends \Eloquent {

	// Add your validation rules here
	public static function rules($id=null)
	{
		return array();
            /* 'hostname' => 'required|hostname|unique:mtas,hostname,'.$id */
		/* ); */
	}

	// Don't forget to fill this array
	protected $fillable = ['mac', 'hostname', 'configfile_id', 'type'];

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
