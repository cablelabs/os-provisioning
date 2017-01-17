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

		$snmp_types = $types = OID::getPossibleEnumValues('type', true);

		// unset null element because otherwise hiding of fields doesnt work with jquery select2
		unset($types[0]);

		// label has to be the same like column in sql table
		return array(
			array('form_type' => 'select', 'name' => 'mibfile_id', 'description' => 'MIB-File', 'value' => $model->html_list(MibFile::all(), 'name')),
			array('form_type' => 'text', 'name' => 'name', 'description' => 'Name', 'options' => ['readonly']),
			array('form_type' => 'text', 'name' => 'name_gui', 'description' => 'Name for Controlling View'),
			array('form_type' => 'text', 'name' => 'oid', 'description' => 'OID', 'options' => ['readonly']),
			array('form_type' => 'text', 'name' => 'syntax', 'description' => 'Syntax', 'options' => ['readonly']),
			array('form_type' => 'text', 'name' => 'access', 'description' => 'Access', 'options' => ['readonly'], 'space' => 1),
			array('form_type' => 'select', 'name' => 'html_type', 'description' => 'HTML Type', 'value' => OID::getPossibleEnumValues('html_type')),

			// array('form_type' => 'checkbox', 'name' => 'oid_table', 'description' => 'SNMP Table Element'),
			array('form_type' => 'select', 'name' => 'type', 'description' => 'SNMP Type', 'value' => $snmp_types, 'select' => $types),
			array('form_type' => 'text', 'name' => 'unit_divisor', 'description' => 'Unit Divisor', 'select' => 'i u'),
			array('form_type' => 'text', 'name' => 'startvalue', 'description' => 'Start Value', 'select' => 'i u'),
			array('form_type' => 'text', 'name' => 'endvalue', 'description' => 'End Value', 'select' => 'i u'),

			array('form_type' => 'textarea', 'name' => 'description', 'description' => 'Description'),
		);
	}

}
