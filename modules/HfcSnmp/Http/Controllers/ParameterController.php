<?php

namespace Modules\HfcSnmp\Http\Controllers;

use Modules\HfcReq\Http\Controllers\HfcReqController;

class ParameterController extends HfcReqController
{
    /**
     * defines the formular fields for the edit and create view
     */
    public function view_form_fields($model = null)
    {
        // not possible
        // if (!$model)
        // 	$model = new Parameter;

        // TODO: shall this read-only Info from OID be shown  ??
        $oid = $model->oid;
        $model->name = $oid ? $oid->name : '';

        // label has to be the same like column in sql table
        $a = [
            ['form_type' => 'text', 'name' => 'netelementtype_id', 'description' => 'NetElementType', 'hidden' => 1],
            ['form_type' => 'text', 'name' => 'oid_id', 'description' => 'OID', 'hidden' => 1],
            ['form_type' => 'text', 'name' => 'name', 'description' => 'Name', 'options' => ['readonly']],
            // array('form_type' => 'text', 'name' => 'html_properties', 'description' => 'HTML Properties'),
            ];

        if ($oid) {
            // only Info's - don't have to be returned on creation or validation
            $a[] = ['form_type' => 'link', 'name' => $oid->oid, 'description' => 'OID', 'url' => route('OID.edit', ['id' => $oid->id]), 'help' => trans('helper.oid_link'), 'space' => 1];

            if ($oid->oid_table) {
                $a[] = ['form_type' => 'checkbox', 'name' => 'table', 'description' => 'Table', 'options' => ['disabled' => 'disabled'], 'help' => trans('helper.oid_table')];
            }
        }

        $b[] = ['form_type' => 'checkbox', 'name' => 'diff_param', 'description' => 'Difference Parameter ?', 'help' => trans('helper.parameter_diff'), 'hidden' => 1, 'space' => 1];
        $b[] = ['form_type' => 'text', 'name' => 'html_frame', 'description' => 'HTML Frame', 'helper' => trans('helper.parameter_html_frame')];
        $b[] = ['form_type' => 'text', 'name' => 'html_id', 'description' => 'HTML ID', 'helper' => trans('helper.parameter_html_id')];
        $b[] = ['form_type' => 'checkbox', 'name' => 'third_dimension', 'description' => '3rd Dimension', 'help' => trans('helper.parameter_3rd_dimension')];
        $b[] = ['form_type' => 'text', 'name' => 'divide_by', 'description' => 'Divide by OID(s)', 'help' => trans('helper.parameter_divide_by'), 'options' => ['placeholder' => '.1.3.4.6.1.127.5, .1.3.4.6.1.118.9, ...']];

        if ($oid && $oid->access == 'read-only') {
            $b[0]['hidden'] = 0;
        }
        if (! $model->parent_id) {
            // $b[1]['hidden'] = 1;
            // else
            $b[3]['hidden'] = 1;
        }
        if ($oid && $oid->oid_table) {
            $b[4]['hidden'] = 1;
        }

        return array_merge($a, $b);
    }

    // Note: This is currently not used - see NetElementTypeController@attach
    public function prepare_rules($rules, $data)
    {
        // don't allow double OID entries for a NetElementType - TODO: add MibFile_id as constraint
        if (isset($data['id'])) {
            $data['oid_id'] = 'unique:parameter,oid_id,'.$data['id'].',id,deleted_at,NULL,netelementtype_id,'.$data['netelementtype_id'];
        }

        return parent::prepare_rules($rules, $data);
    }
}
