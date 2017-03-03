<?php

namespace Modules\HfcSnmp\Http\Controllers;

use Modules\HfcReq\Http\Controllers\HfcReqController;

class ParameterController extends HfcReqController {

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
		$a = array(
			array('form_type' => 'text', 'name' => 'netelementtype_id', 'description' => 'NetElementType', 'hidden' => 1),
			array('form_type' => 'text', 'name' => 'oid_id', 'description' => 'OID', 'hidden' => 1),
			array('form_type' => 'text', 'name' => 'name', 'description' => 'Name', 'options' => ['readonly']),
			);

		if ($oid)
		{
			$a[] = array('form_type' => 'link', 'name' => $oid->oid, 'description' => 'OID', 'url' => route('OID.edit', ['id' => $oid->id]), 'help' => trans('helper.oid_link'), 'space' => 1);

			if ($oid->oid_table)
				$a[] = array('form_type' => 'checkbox', 'name' => 'table', 'description' => 'Table', 'options' => ['disabled' => 'disabled'], 'help' => trans('helper.oid_table'));

			$a[] = array('form_type' => 'text', 'name' => 'indices', 'description' => 'Indices', 'help' => trans('helper.parameter_indices'), 'space' => 1);
		}

		if ($model->parent_id)
			$a[] = array('form_type' => 'checkbox', 'name' => '3rd_dimension', 'description' => '3rd Dimension', 'help' => trans('helper.parameter_3rd_dimension'));

		$b = array(
				array('form_type' => 'text', 'name' => 'html_frame', 'description' => 'HTML Frame'),
				array('form_type' => 'text', 'name' => 'html_id', 'description' => 'HTML ID'),
				// array('form_type' => 'text', 'name' => 'html_properties', 'description' => 'HTML Properties'),
			);

		return array_merge($a, $b);

	}

	// Note: This is currently not used - see NetElementTypeController@attach
	public function prepare_rules($rules, $data)
	{
		// don't allow double OID entries for a NetElementType - TODO: add MibFile_id as constraint
		if (isset($data['id']))
			$data['oid_id'] = 'unique:parameter,oid_id,'.$data['id'].',id,deleted_at,NULL,netelementtype_id,'.$data['netelementtype_id'];

		return parent::prepare_rules($rules, $data);
	}

}
