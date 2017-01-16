<?php

namespace Modules\HfcSnmp\Http\Controllers;

class ParameterController extends \BaseController {

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
		$model->oid = $oid->oid;
		$model->name = $oid->name;
		// $model->syntax = $oid->syntax;
		// $model->access = $oid->access;
		// $model->html_type = $oid->html_type;
		// $model->type = $oid->type;

		// label has to be the same like column in sql table
		return array(
			array('form_type' => 'text', 'name' => 'name', 'description' => 'Name', 'options' => ['readonly']),
			array('form_type' => 'link', 'name' => $oid->oid, 'description' => 'OID', 'url' => route('OID.edit', ['id' => $oid->id]), 'help' => trans('helper.oid_link')),
			// array('form_type' => 'text', 'name' => 'syntax', 'description' => 'Syntax', 'options' => ['readonly']),
			// array('form_type' => 'text', 'name' => 'access', 'description' => 'Access', 'options' => ['readonly']),
			// array('form_type' => 'text', 'name' => 'html_type', 'description' => 'HTML Type', 'options' => ['readonly']),
			// array('form_type' => 'text', 'name' => 'type', 'description' => 'SNMP Type', 'options' => ['readonly'], 'space' => 1),

			array('form_type' => 'text', 'name' => 'html_frame', 'description' => 'HTML Frame'),
			array('form_type' => 'text', 'name' => 'html_id', 'description' => 'HTML ID'),
			array('form_type' => 'text', 'name' => 'html_properties', 'description' => 'HTML Properties'),

			// array('form_type' => 'checkbox', 'name' => 'oid_table', 'description' => 'SNMP Table Element'),
			// array('form_type' => 'text', 'name' => 'type_array', 'description' => 'Type Array (?)', 'space' => 1),
			//array('form_type' => 'textarea', 'name' => 'phpcode_pre', 'description' => 'PHP Code Pre-SNMP-Execution'),
			//array('form_type' => 'textarea', 'name' => 'phpcode_post', 'description' => 'PHP Code Post-SNMP-Execution'),
			// array('form_type' => 'textarea', 'name' => 'description', 'description' => 'Description'),
		);
	}

	// Note: This is currently not used - see NetElementTypeController@attach
	public function prepare_rules($rules, $data)
	{
		// don't allow double OID entries for a NetElementType
		$data['oid_id'] = 'unique:parameter,oid_id,'.$id.',id,deleted_at,NULL,netelementtype_id,'.$data['netelementtype_id'];

		return parent::prepare_rules($rules, $data);
	}

}
