<?php

namespace Modules\ProvVoip\Entities;

// Model not found? execute composer dump-autoload in lara root dir
class CarrierCode extends \BaseModel {

    // The associated SQL table for this Model
    public $table = 'carriercode';


	// Add your validation rules here
	public static function rules($id=null)
	{
		return array(
			'country_code' => 'required|numeric',
			'prefix_number' => 'required|numeric',
			'number' => 'required|numeric',
			'mta_id' => 'required|exists:mta,id|min:1',
			'port' => 'required|numeric|min:1',
			'active' => 'required|boolean',
		);
	}

	// Don't forget to fill this array
	protected $fillable = ['carrier_code', 'company'];
}
