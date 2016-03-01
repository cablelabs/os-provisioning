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

		# carrier codes of the carriers to be on top of the list
		$favorite_carriers = array(
			'D150', # Telekom
			'D057', # EnviaTel
		);

		$normal_carrier_list = array();
		$favorite_carrier_list = array();

		foreach ($this::all()->sortBy('company') as $carrier)
		{
			$id = $carrier->id;
			$carrier_code = $carrier->carrier_code;
			$company = $carrier->company;

			$entry = $company;
			if (boolval($carrier_code)) {
				$entry .= ' ('.$carrier_code.')';
			}

			# add to favorite or normal carriers
			if (array_search($carrier_code, $favorite_carriers) !== False) {
				$favorite_carrier_list[$id] = $entry;
			}
			else {
				$normal_carrier_list[$id] = $entry;
			}
		}

		// build result array (with favorites on top)
		// attention! don't change the keys – they refer directly to ids in table carriercode!
		// therefore we cannot use array_merge
		$result = array();
		foreach ($favorite_carrier_list as $id => $entry) {
			$result[$id] = $entry;
		}
		foreach ($normal_carrier_list as $id => $entry) {
			$result[$id] = $entry;
		}

		return $result;
	}

}
