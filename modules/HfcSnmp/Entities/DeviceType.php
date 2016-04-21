<?php

namespace Modules\HfcSnmp\Entities;

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
	public static function get_view_header()
	{
		return 'Device Type';
	}

	// link title in index view
	public function get_view_link_title()
	{
		return $this->name;
	}

	/**
	 * link with devices
	 */
	public function devices()
	{
		return $this->hasMany('Modules\HfcSnmp\Entities\Device', 'devicetype_id');
	}

	/**
	 * link with
	 */
	public function snmpmibs()
	{
		return $this->hasMany('Modules\HfcSnmp\Entities\SnmpMib', 'devicetype_id');
	}

    // returns all objects that are related to a DeviceType
    public function view_has_many()
    {
    	if (0) // disable
			return array(
				'Device' => $this->devices,
				'SnmpMib' => $this->snmpmibs
			);

		// Testing view_has_many() new API
        return [
            'Test' => ['Device' => $this->devices],
            'Test2' => ['SnmpMib' => $this->snmpmibs, 'View Stuff' => ['view' => 'test'], 'View 2' => ['view' => 'test'], 'Html Stuff' => ['html' => '<li><a href=google.de>Test</a></li>']],
            'Test3' => ['SnmpMib' => $this->snmpmibs, 'Device' => $this->devices, 'Hua' => 'test']
        ];
    }
}