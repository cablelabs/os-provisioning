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


	/**
	 * return a list [id => carrier (carriercode)] of all carriers
	 */
	public function carrier_list()
	{
		$favorite_carriers = array(
			'D001', # Telekom
			'D057', # EnviaTel
		);

		$carrier_list = array();
		$fav_list = array();

		/* foreach ($this::all()->orderBy('company') as $carriercode) */
		foreach ($this::all()->sortBy('company') as $carrier)
		{
			$id = $carrier->id;
			$company = $carrier->company;
			$carrier_code = $carrier->carrier_code;

			if ($id > 1) {
				# add to carriers
				$carrier_list[$id] = $company.' ('.$carrier_code.')';

				# add to favorites?
				if (array_search($carrier_code, $favorite_carriers) !== False) {
					$fav_list[$id] = $company.' ('.$carrier_code.')';
				}
			}
		}

		return array_merge(['0' => '–'], $fav_list, ['0' => '––––––––––––––––'], $carrier_list);
	}

}
