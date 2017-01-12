<?php

namespace Modules\HfcSnmp\Entities;

class OID extends \BaseModel {

	public $table = 'oid';

	// Add your validation rules here
	public static function rules($id = null)
    {
        return array(
			'oid' => 'required',
        );
    }

	// Name of View
	public static function view_headline()
	{
		return 'OID';
	}

	// link title in index view
	public function view_index_label()
	{
		return ['index' => [$this->name, $this->oid, $this->access],
		        'index_header' => ['Name', 'OID', 'Access'],
		        'header' => $this->name.' - '.$this->oid];
	}

	public function index_list()
	{
		return $this->orderBy('oid')->simplePaginate(1000);
	}


	/**
	 * Relations
	 */
	public function mibfile()
	{
		return $this->belongsTo('Modules\HfcSnmp\Entities\MibFile');
	}

	// public function netelementtypes()
	// {
	// 	return $this->belongsToMany('Modules\HfcReq\Entities\NetElementType', 'netelementtype_oid', 'oid_id', 'netelementtype_id');
	// }

	public function parameters()
	{
		// NOTE: This should be done with eager loading if not already done by laravel automatically, because oid relation is needed close to all the time
		return $this->HasMany('Modules\HfcSnmp\Entities\Parameter');
		// ->with('Modules\HfcSnmp\Entities\OID')->get();
	}

	public function view_belongs_to ()
	{
		return $this->mibfile;
	}

}