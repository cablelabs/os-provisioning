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
	public static function view_headline()
	{
		return 'Device Type';
	}

	// link title in index view
	public function view_index_label()
	{
		return ['index' => [$this->name, $this->vendor, $this->version],
		        'index_header' => ['Name', 'Vendor', 'Version'],
		        'header' => $this->name];
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
				'Edit' => [
					'hua' => ['class' => 'Device', 'relation' => $this->devices],
					'hua2' => ['class' => 'SnmpMib', 'relation' => $this->snmpmibs]
				]
			);

		// Testing view_has_many() API v2
		return [
			'Test' =>  ['Device Class Header' => ['class' => 'Device',
												  'relation' => $this->devices,
												  'options' => ['hide_create_button' => 1, 'hide_delete_button' => 1]],
						'Snmp Mib Header' => ['class' => 'SnmpMib', 'relation' => $this->snmpmibs]],
			'Test2' => ['SnmpMib Header' => ['class' => 'SnmpMib', 'relation' => $this->snmpmibs],
						'View Stuff Header' => ['view' => 'test'], 'View 2' => ['view' => 'test'],
						'Html Stuff Header' => ['html' => '<li><a href=google.de>Test</a></li>']],
			'Test3' => ['hua' => ['class' => 'Device', 'relation' => $this->devices],
						'hua2' => ['class' => 'SnmpMib', 'relation' => $this->snmpmibs],
						'Test Panel' => ['html' => 'HUA']]
		];
	}
}