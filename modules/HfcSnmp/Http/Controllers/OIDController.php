<?php

namespace Modules\HfcSnmp\Http\Controllers;

use Modules\HfcSnmp\Entities\MibFile;
use Modules\HfcSnmp\Entities\OID;

class OIDController extends \BaseController {

    /**
     * defines the formular fields for the edit and create view
     */
	public function view_form_fields($model = null)
	{
		if (!$model)
			$model = new OID;

		// label has to be the same like column in sql table
		return array(
			array('form_type' => 'select', 'name' => 'mibfile_id', 'description' => 'MIB-File', 'value' => $model->html_list(MibFile::all(), 'name')),
			array('form_type' => 'text', 'name' => 'oid', 'description' => 'OID', 'options' => ['readonly']),
			array('form_type' => 'text', 'name' => 'name', 'description' => 'Name', 'options' => ['readonly']),
			array('form_type' => 'text', 'name' => 'syntax', 'description' => 'Syntax', 'options' => ['readonly']),
			array('form_type' => 'text', 'name' => 'access', 'description' => 'Access', 'options' => ['readonly'], 'space' => 1),
			array('form_type' => 'text', 'name' => 'field', 'description' => 'Field Name'),

			array('form_type' => 'select', 'name' => 'html_type', 'description' => 'HTML Type', 'value' => OID::getPossibleEnumValues('html_type')),
			array('form_type' => 'text', 'name' => 'html_frame', 'description' => 'HTML Frame'),
			array('form_type' => 'text', 'name' => 'html_properties', 'description' => 'HTML Properties', 'space' => 1),

			array('form_type' => 'checkbox', 'name' => 'oid_table', 'description' => 'SNMP Table Element'),
			array('form_type' => 'select', 'name' => 'type', 'description' => 'SNMP Type', 'value' => OID::getPossibleEnumValues('type', true)),
			array('form_type' => 'text', 'name' => 'type_array', 'description' => 'Type Array (?)', 'space' => 1),
			//array('form_type' => 'textarea', 'name' => 'phpcode_pre', 'description' => 'PHP Code Pre-SNMP-Execution'),
			//array('form_type' => 'textarea', 'name' => 'phpcode_post', 'description' => 'PHP Code Post-SNMP-Execution'),
			array('form_type' => 'textarea', 'name' => 'description', 'description' => 'Description'),
		);
	}

}
