<?php

namespace Modules\ProvVoip\Entities;

use Modules\ProvBase\Entities\ProvBase;

class ProvVoip extends \BaseModel {

	// The associated SQL table for this Model
	protected $table = 'provvoip';

	// Don't forget to fill this array
	// protected $fillable = ['startid_mta'];

	// Add your validation rules here
	public static function rules($id = null)
	{
		return array(
		);
	}

	// Name of View
	public static function view_headline()
	{
		return 'ProvVoip Config';
	}

	// link title in index view
	public function view_index_label()
	{
		return "ProvVoip";
	}

    public static function boot()
    {
        parent::boot();

        ProvVoip::observe(new ProvVoipObserver);
        ProvVoip::observe(new \App\SystemdObserver);
    }

}


class ProvVoipObserver
{
	public function updated($provvoip)
	{
		ProvBase::first()->make_dhcp_glob_conf();
	}
}