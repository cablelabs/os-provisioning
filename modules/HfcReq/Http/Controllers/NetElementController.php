<?php

namespace Modules\HfcReq\Http\Controllers;

use Modules\HfcReq\Entities\NetElement;
use Modules\HfcReq\Entities\NetElementType;
use Modules\HfcBase\Http\Controllers\HfcBaseController;
use Modules\HfcSnmp\Http\Controllers\SnmpController;

class NetElementController extends HfcBaseController {

    /**
     * defines the formular fields for the edit and create view
     */
	public function view_form_fields($model = null)
	{
		$model = $model ? : new NetElement;

		$empty_field = isset($model->id);
		$parents 	 = $model->html_list(NetElement::get(['id','name']), 'name', $empty_field);
		$kml_files   = $model->kml_files();


		// label has to be the same like column in sql table
		return array(
			array('form_type' => 'text', 'name' => 'name', 'description' => 'Name'),
			// array('form_type' => 'select', 'name' => 'type', 'description' => 'Type', 'value' => ['NET' => 'NET', 'CMTS' => 'CMTS', 'DATA' => 'DATA', 'CLUSTER' => 'CLUSTER', 'NODE' => 'NODE', 'AMP' => 'AMP']),
			array('form_type' => 'select', 'name' => 'netelementtype_id', 'description' => 'NetElement Type', 'value' => $model->html_list(NetElementType::get(['id', 'name']), 'name'), 'hidden' => 0),
			// net is automatically detected in Observer
			// array('form_type' => 'select', 'name' => 'net', 'description' => 'Net', 'value' => $nets),
			array('form_type' => 'text', 'name' => 'ip', 'description' => 'IP address'),
			array('form_type' => 'text', 'name' => 'link', 'description' => 'HTML Link'),
			array('form_type' => 'text', 'name' => 'pos', 'description' => 'Geoposition'),
			array('form_type' => 'select', 'name' => 'parent_id', 'description' => 'Parent Object', 'value' => $parents),
			// array('form_type' => 'select', 'name' => 'state', 'description' => 'State', 'value' => ['OK' => 'OK', 'YELLOW' => 'YELLOW', 'RED' => 'RED'], 'options' => ['readonly']),
			array('form_type' => 'text', 'name' => 'options', 'description' => 'Options'),

			array('form_type' => 'select', 'name' => 'kml_file', 'description' => 'Choose KML file', 'value' => $kml_files),
			array('form_type' => 'file', 'name' => 'kml_file_upload', 'description' => 'or: Upload KML file', 'space' => 1),

			array('form_type' => 'text', 'name' => 'community_ro', 'description' => 'Community RO'),
			array('form_type' => 'text', 'name' => 'community_rw', 'description' => 'Community RW'),
			array('form_type' => 'text', 'name' => 'address1', 'description' => 'Address Line 1'),
			array('form_type' => 'text', 'name' => 'address2', 'description' => 'Address Line 2'),
			array('form_type' => 'text', 'name' => 'address3', 'description' => 'Address Line 3'),
			array('form_type' => 'textarea', 'name' => 'descr', 'description' => 'Description'),
		);

	}


	protected function get_form_tabs($view_var)
	{
		return [
			['name' => 'Edit', 'route' => 'NetElement.edit', 'link' => [$view_var->id]],
			['name' => 'Controlling', 'route' => 'NetElement.controlling_edit', 'link' => [$view_var->id]]
		];
	}


	/**
	 * Overwrites the base method to handle file uploads
	 */
	public function store($redirect = true)
	{
		// check and handle uploaded KML files
		$this->handle_file_upload('kml_file', static::get_model_obj()->kml_path);

		return parent::store();

		// $ret = parent::store();
		// NetElement::relation_index_build_all();
		// return $ret;
	}

	/**
	 * Overwrites the base method to handle file uploads
	 */
	public function update($id)
	{
		// check and handle uploaded KML files
		$this->handle_file_upload('kml_file', static::get_model_obj()->kml_path);

		return parent::update($id);
	}


}
