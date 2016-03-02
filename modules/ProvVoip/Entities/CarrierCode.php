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
	 * Return a list [id => carrier (carriercode)] of all carriers.
	 * This list is prepared for the use in a form's <select>
	 *	- first comes the default value (=no carrier)
	 *	- second are the favorites
	 *	- then the rest
	 *
	 *	@author Patrick Reichel
	 *
	 *	@return array containing all carriers
	 */
	public function carrier_list_for_form_select()
	{

		# carrier codes of the carriers to be on top of the list
		# the given sorting will be the sorting of the <select>
		$favorite_carriers = array(
			'D001', # Telekom
			'D201', # 1&1
		);

		$normal_carrier_list = array();
		$favorite_carrier_list = array();
		$no_carrier_list = array();

		// get all the carrier code data and put it into arrays
		foreach (CarrierCode::orderBy('company')->orderBy('carrier_code')->get() as $carrier)
		{
			$id = $carrier->id;
			$carrier_code = $carrier->carrier_code;
			$company = $carrier->company;

			$entry = $company;
			if (boolval($carrier_code)) {
				$entry .= ' ('.$carrier_code.')';
			}

			// helper to check if current carrier is favorite and – if yes – in which position it shall occur
			$fav_pos = array_search($carrier_code, $favorite_carriers);

			// add to the corresponding array
			if (!boolval($carrier_code)) {
				$no_carrier_list[$id] = $entry;
			}
			elseif ($fav_pos !== False) {
				$favorite_carrier_list[$fav_pos] = array($id, $entry);
			}
			else {
				$normal_carrier_list[$id] = $entry;
			}
		}


		// build result array (with favorites on top)
		// attention! don't change the keys – they refer directly to ids in table carriercode!
		// therefore we cannot use array_merge, but have to do it by hand!

		// first element is the null element ⇒ this will be selected by default in a new form
		// so we initialize the result array with the null element
		$result = $no_carrier_list;

		// sort favorites by key (=position in favorite_carriers) to get correct order
		ksort($favorite_carrier_list);

		// then add the favorites sorted like in favorite_carriers
		foreach ($favorite_carrier_list as $fav) {
			$id = $fav[0];
			$entry = $fav[1];
			$result[$id] = $entry;
		}

		// finally add the rest of the carriers
		foreach ($normal_carrier_list as $id => $entry) {
			$result[$id] = $entry;
		}

		return $result;
	}

}
