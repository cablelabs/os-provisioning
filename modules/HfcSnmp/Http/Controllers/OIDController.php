<?php

namespace Modules\HfcSnmp\Http\Controllers;

use Modules\HfcSnmp\Entities\{ MibFile, OID};

class OIDController extends \BaseController {

    /**
     * defines the formular fields for the edit and create view
     */
	public function view_form_fields($model = null)
	{
		if (!$model)
			$model = new OID;

		$snmp_types = $snmp_types_select = OID::getPossibleEnumValues('type', true);
		$html_types = OID::getPossibleEnumValues('html_type');

		$format = 'qam16=1, qam64=2, qam256=3 or qam16(1), qam64(2), qam256(3)';

// d($html_types, $snmp_types_select);

		// unset null element because otherwise hiding of fields doesnt work with jquery select2
		unset($snmp_types_select[0]);

		// label has to be the same like column in sql table
		return array(
			array('form_type' => 'select', 'name' => 'mibfile_id', 'description' => 'MIB-File', 'value' => $model->html_list(MibFile::all(), 'name')),
			array('form_type' => 'text', 'name' => 'name', 'description' => 'Name', 'options' => ['readonly']),
			array('form_type' => 'text', 'name' => 'name_gui', 'description' => 'Name for Controlling View'),
			array('form_type' => 'text', 'name' => 'oid', 'description' => 'OID', 'options' => ['readonly']),
			array('form_type' => 'text', 'name' => 'syntax', 'description' => 'Syntax', 'options' => ['readonly']),
			array('form_type' => 'text', 'name' => 'access', 'description' => 'Access', 'options' => ['readonly'], 'space' => 1),
			array('form_type' => 'select', 'name' => 'html_type', 'description' => 'HTML Type', 'value' => $html_types, 'select' => $html_types),

			array('form_type' => 'checkbox', 'name' => 'oid_table', 'description' => 'Is SNMP Table'),
			array('form_type' => 'select', 'name' => 'type', 'description' => 'SNMP Type', 'value' => $snmp_types, 'select' => $snmp_types_select),
			array('form_type' => 'text', 'name' => 'unit_divisor', 'description' => 'Unit Divisor', 'select' => 'i u'),
			array('form_type' => 'text', 'name' => 'startvalue', 'description' => 'Start Value', 'select' => 'select i u'),
			array('form_type' => 'text', 'name' => 'stepsize', 'description' => 'Stepsize', 'select' => 'select i u'),
			array('form_type' => 'text', 'name' => 'endvalue', 'description' => 'End Value', 'select' => 'select i u'),
			array('form_type' => 'textarea', 'name' => 'value_set', 'description' => 'Possible Values for Select', 'select' => 'select', 'options' => ['placeholder' => $format], 'help' => 'These values are prioritized before Start & End Value'),

			array('form_type' => 'textarea', 'name' => 'description', 'description' => 'Description'),
		);
	}

}
