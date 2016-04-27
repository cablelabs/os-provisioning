<?php

namespace Modules\ProvVoip\Entities;

// Model not found? execute composer dump-autoload in lara root dir
class PhoneTariff extends \BaseModel {

    // The associated SQL table for this Model
	public $table = 'phonetariff';

	// Add your validation rules here
	public static function rules($id=null)
	{
		// Port unique in the appropriate mta (where mta_id=mta_id and deleted_at=NULL)

		return array(
			'external_identifier' => 'required',
			'name' => 'required',
			'usable' => 'required|boolean',
		);
	}


	// Name of View
	public static function get_view_header()
	{
		return 'PhoneTariffs';
	}

	// link title in index view
	public function get_view_link_title()
	{
		return $this->name." (".$this->type.")";
	}

	/**
	 * Returns all purchase tariffs that are flagged as usable.
	 *
	 * @author Patrick Reichel
	 *
	 * @return array with phonetariff.id=>phonetariff.name
	 */
	public static function get_purchase_tariffs() {

		return PhoneTariff::__get_tariffs('purchase');
	}

	/**
	 * Returns all sales tariffs that are flagged as usable.
	 *
	 * @author Patrick Reichel
	 *
	 * @return array with phonetariff.id=>phonetariff.name
	 */
	public static function get_sale_tariffs() {

		return PhoneTariff::__get_tariffs('sale');

	}


	/**
	 * Return a tariff for a given type.
	 *
	 * @author Patrick Reichel
	 *
	 * @param $type The tariff type as string (currently purchase and sale).
	 *
	 * @return array with phonetariff.id=>phonetariff.name
	 */
	private static function __get_tariffs($type) {

		$supported_types = ['purchase', 'sale'];

		$ret = array();

		// check if valid type is given
		if (!in_array($type, $supported_types)) {
			throw new \InvalidArgumentException('Type must be in ['.implode(', ', $supported_types).']');
		}

		// can be used in raw statement; $type is well known and not given from user input
		$tariffs = PhoneTariff::where('type', $type)->where('usable', 1)->get();

		foreach ($tariffs as $tariff) {
			$ret[$tariff->id] = $tariff->name;
		}
		return $ret;
	}

}
