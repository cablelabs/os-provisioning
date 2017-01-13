<?php

namespace Modules\HfcSnmp\Entities;

class Parameter extends \BaseModel {

	public $table = 'parameter';

	// Add your validation rules here
	public static function rules($id = null)
	{
		return array(
			// 'oid' => 'required',
		);
	}

	// Name of View
	public static function view_headline()
	{
		return 'Parameter';
	}

	// link title in index view
	public function view_index_label()
	{
		return ['index' => [$this->oid->name, $this->oid->oid, $this->access],
				'index_header' => ['Name', 'OID', 'Access'],
				'header' => $this->oid->name.' - '.$this->oid->oid];
	}

	public function index_list()
	{
		$eager_loading_model = new OID;

		return $this->orderBy('id')->with($eager_loading_model->table)->get();
	}


	/**
	 * Relations
	 */
	public function oid()
	{
		return $this->belongsTo('Modules\HfcSnmp\Entities\OID', 'oid_id');
	}

	public function netelementtype()
	{
		return $this->belongsTo('Modules\HfcReq\Entities\NetElementType', 'netelementtype_id');
	}

	// public function view_belongs_to ()
	// {
	// 	return $this->mibfile;
	// }

}