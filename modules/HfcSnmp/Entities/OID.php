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
		return 'MIB-File';
	}

	// link title in index view
	public function view_index_label()
	{
		$devicetype = $this->devicetype ? $this->devicetype->name : '';

		return ['index' => [$devicetype, $this->field, $this->oid, $this->html_type, $this->description],
		        'index_header' => ['Device Type', 'Field Name', 'SNMP OID', 'HTML Type', 'Description'],
		        'header' => $this->field.' - '.$this->oid];
	}

	/**
	 * link with devicetype
	 */
	public function devicetype()
	{
		return $this->belongsTo('Modules\HfcSnmp\Entities\DeviceType');
	}

    /**
     * return all DeviceType Objects for Device
     */
    public function devicetypes ()
    {
        return DeviceType::all();
    }


	public function view_belongs_to ()
	{
		return $this->devicetype;
	}
}