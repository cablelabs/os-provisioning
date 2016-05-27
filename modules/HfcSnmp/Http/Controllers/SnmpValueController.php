<?php

namespace Modules\HfcSnmp\Http\Controllers;

use Models\SnmpValue;

class SnmpValueController extends \BaseController {

    /**
     * defines the formular fields for the edit and create view
     */
	public function view_form_fields($model = null)
	{
		// label has to be the same like column in sql table
		return array(
			array('form_type' => 'text', 'name' => 'device_id', 'description' => 'Device ID'),
			array('form_type' => 'text', 'name' => 'snmpmib_id', 'description' => 'SNMP MIB ID'),
			array('form_type' => 'text', 'name' => 'oid_index', 'description' => 'OID Index (for Tables)'),
			array('form_type' => 'text', 'name' => 'value', 'description' => 'SNMP Value'),
		);
	}

}
