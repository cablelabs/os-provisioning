<?php

namespace Modules\HfcSnmp\Http\Controllers;

use Modules\HfcSnmp\Entities\OID;
use Modules\HfcSnmp\Entities\MibFile;

class OIDController extends \BaseController
{
    /**
     * defines the formular fields for the edit and create view
     */
    public function view_form_fields($model = null)
    {
        if (! $model) {
            $model = new OID;
        }

        $snmp_types = $snmp_types_select = OID::getPossibleEnumValues('type', true);
        $html_types = OID::getPossibleEnumValues('html_type');

        $format = 'qam16=1, qam64=2, qam256=3 or qam16(1), qam64(2), qam256(3)';

        // d($html_types, $snmp_types_select);

        // unset null element because otherwise hiding of fields doesnt work with jquery select2
        unset($snmp_types_select[0]);

        // label has to be the same like column in sql table
        return [
            ['form_type' => 'select', 'name' => 'mibfile_id', 'description' => 'MIB-File', 'value' => $model->html_list(MibFile::all(), 'name')],
            ['form_type' => 'text', 'name' => 'name', 'description' => 'Name', 'options' => ['readonly']],
            ['form_type' => 'text', 'name' => 'name_gui', 'description' => 'Name for Controlling View'],
            ['form_type' => 'text', 'name' => 'oid', 'description' => 'OID', 'options' => ['readonly']],
            ['form_type' => 'text', 'name' => 'syntax', 'description' => 'Syntax', 'options' => ['readonly']],
            ['form_type' => 'text', 'name' => 'access', 'description' => 'Access', 'options' => ['readonly'], 'space' => 1],
            ['form_type' => 'select', 'name' => 'html_type', 'description' => 'HTML Type', 'value' => $html_types, 'select' => $html_types],

            ['form_type' => 'checkbox', 'name' => 'oid_table', 'description' => 'Is SNMP Table'],
            ['form_type' => 'select', 'name' => 'type', 'description' => 'SNMP Type', 'value' => $snmp_types, 'select' => $snmp_types_select],
            ['form_type' => 'text', 'name' => 'unit_divisor', 'description' => 'Unit Divisor', 'select' => 'i u'],
            ['form_type' => 'text', 'name' => 'startvalue', 'description' => 'Start Value', 'select' => 'select i u'],
            ['form_type' => 'text', 'name' => 'stepsize', 'description' => 'Stepsize', 'select' => 'select i u'],
            ['form_type' => 'text', 'name' => 'endvalue', 'description' => 'End Value', 'select' => 'select i u'],
            ['form_type' => 'textarea', 'name' => 'value_set', 'description' => 'Possible Values for Select', 'select' => 'select', 'options' => ['placeholder' => $format], 'help' => 'These values are prioritized before Start & End Value'],

            ['form_type' => 'textarea', 'name' => 'description', 'description' => 'Description'],
        ];
    }
}
