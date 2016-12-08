<?php

namespace Modules\HfcReq\Entities;

class DeviceType extends \BaseModel {

	// The associated SQL table for this Model
	public $table = 'devicetype';


	// Add your validation rules here
	public static $rules = [
		// 'title' => 'required'
	];


	// Add your validation rules here
	public static function rules($id = null)
	{
		return array(
			'name' => 'required'
		);
	}

	// Name of View
	public static function view_headline()
	{
		// Note: single underscore has error in view as consequence
		return 'Device  Type';
	}

	// link title in index view
	public function view_index_label()
	{
		return ['index' => [$this->name, $this->vendor, $this->version],
		        'index_header' => ['Name', 'Vendor', 'Version'],
		        'header' => $this->name];
	}

	/**
	 * Relations
	 */
	public function netelements()
	{
		return $this->hasMany('Modules\HfcReq\Entities\NetElement', 'devicetype_id');
	}


	// returns all objects that are related to a DeviceType
	public function view_has_many()
	{
		if (0) // disable
			return array(
				'Edit' => [
					'hua' => ['class' => 'NetElement', 'relation' => $this->netelements],
				]
			);

		// Testing view_has_many() API v2
		return [
			'Test' =>  ['NetElement Class Header' => ['class' => 'NetElement',
												  'relation' => $this->netelements,
												  'options' => ['hide_create_button' => 1, 'hide_delete_button' => 1]]]

		];
	}
}