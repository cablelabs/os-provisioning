<?php

namespace Modules\HfcReq\Entities;

class NetElementType extends \BaseModel {

	// The associated SQL table for this Model
	public $table = 'netelementtype';


	// Add your validation rules here
	public static function rules($id = null)
	{
		return array(
			'name' => 'required',
		);
	}

	// Name of View
	public static function view_headline()
	{
		return 'NetElementType';
	}

	// link title in index view
	public function view_index_label()
	{
		return ['index' => [$this->name],
		        'index_header' => ['Name'],
		        'header' => $this->name];
	}

	/**
	 * Relations
	 */
	public function netelements()
	{
		return $this->hasMany('Modules\HfcReq\Entities\NetElement', 'netelement_id');
	}

	public function devicetypes()
	{
		return $this->hasMany('Modules\HfcReq\Entities\DeviceType', 'netelement_id');
	}

	// returns all objects that are related to a DeviceType
	public function view_has_many()
	{
		return [];
	}
}