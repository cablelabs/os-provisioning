<?php

namespace Modules\HfcSnmp\Http\Controllers;

use Models\SnmpMib;

class SnmpMibController extends \BaseModuleController {

    /**
     * defines the formular fields for the edit and create view
     */
	public function view_form_fields($model = null)
	{
		if (!$model)
			$model = new SnmpMib;

		// label has to be the same like column in sql table
		return array(
			array('form_type' => 'select', 'name' => 'devicetype_id', 'description' => 'Device Type', 'value' => $model->html_list($model->devicetypes(), 'name')),

			array('form_type' => 'text', 'name' => 'oid', 'description' => 'SNMP Oid'),
			array('form_type' => 'text', 'name' => 'field', 'description' => 'Field Name'),

			array('form_type' => 'select', 'name' => 'html_type', 'description' => 'HTML Type', 'value' => ['text'=>'text','select'=>'select','groupbox'=>'groupbox','textarea'=>'textarea']),
			array('form_type' => 'text', 'name' => 'html_frame', 'description' => 'HTML Frame'),
			array('form_type' => 'text', 'name' => 'html_properties', 'description' => 'HTML Properties'),

			array('form_type' => 'select', 'name' => 'oid_table', 'description' => 'SNMP Table Element', 'value' => ['0' => 'No', '1' => 'Yes']),
			array('form_type' => 'select', 'name' => 'type', 'description' => 'SNMP Type', 'value' => ['i'=>'i','u'=>'u','s'=>'s','x'=>'x','d'=>'d','n'=>'n','o'=>'o','t'=>'t','a'=>'a','b'=>'b']),
			array('form_type' => 'text', 'name' => 'type_array', 'description' => 'Type Array (?)'),
			//array('form_type' => 'textarea', 'name' => 'phpcode_pre', 'description' => 'PHP Code Pre-SNMP-Execution'),
			//array('form_type' => 'textarea', 'name' => 'phpcode_post', 'description' => 'PHP Code Post-SNMP-Execution'),
			array('form_type' => 'textarea', 'name' => 'description', 'description' => 'Description'),
		);
	}

}
