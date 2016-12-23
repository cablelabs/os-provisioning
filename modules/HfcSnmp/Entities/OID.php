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
		// $devicetype = $this->devicetype ? $this->devicetype->name : '';

		return ['index' => [$this->name, $this->oid, $this->access],
		        'index_header' => ['Name', 'OID', 'Access'],
		        'header' => $this->name.' - '.$this->oid];



		// return ['index' => [$devicetype, $this->field, $this->oid, $this->html_type, $this->description],
		//         'index_header' => ['Device Type', 'Field Name', 'SNMP OID', 'HTML Type', 'Description'],
		//         'header' => $this->field.' - '.$this->oid];
	}

	/**
	 * Relations
	 */
	public function mibfile()
	{
		return $this->belongsTo('Modules\HfcSnmp\Entities\MibFile');
	}

	public function view_belongs_to ()
	{
		return $this->mibfile;
	}


	// public function index_list ()
	// {
	// 	return $this->orderBy('oid');
	// }

}