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

	/**
	 * View Stuff
	 */

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

	public function index_list ()
	{
		$types = $this->orderBy('id')->get();
		$undeletables = ['Net', 'Cluster'];

		foreach ($types as $type)
		{
			if (in_array($type->name, $undeletables))
				$type->index_delete_disabled = true;
		}

		return $types;
	}

	// returns all objects that are related to a DeviceType
	public function view_has_many()
	{
		return [];
	}

	/**
	 * Relations
	 */
	public function netelements()
	{
		return $this->hasMany('Modules\HfcReq\Entities\NetElement', 'netelementtype_id');
	}

	public function devicetypes()
	{
		return $this->hasMany('Modules\HfcReq\Entities\DeviceType', 'netelementtype_id');
	}

}