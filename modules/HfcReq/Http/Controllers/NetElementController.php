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

		// parse which netelementtype we want to edit/create
		// NOTE: this is for auto reload via HTML GET
		$type = 0;
		if (isset($_GET['netelementtype_id']))
			$type = $_GET['netelementtype_id'];
		elseif ($model->netelementtype)
			$type = $model->netelementtype->get_base_type();

		/*
		 * provisioning device
		 */
		$prov_device = [];
		$prov_device_hidden = 1;

		if ($type == 3) // cmts
			$prov_device = $model->html_list(\Modules\ProvBase\Entities\Cmts::get(['id', 'hostname']), 'hostname', $empty_field);

		if ($type == 4 || $type == 5) // amp || node
			$prov_device = $model->html_list(\DB::table('modem')->where('deleted_at', '=', NULL)->get(['id', 'name']), ['id', 'name'], $empty_field, ': ');

		if ($prov_device)
			$prov_device_hidden = 0;

		/*
		 * cluster: rf card settings
		 */
		$options_array = array('form_type' => 'text', 'name' => 'options', 'description' => 'Options');
		if ($model->netelementtype && $model->netelementtype->get_base_type() == 2)
		{
			$options_array = array('form_type' => 'select', 'name' => 'options', 'description' => 'RF Card Setting (DSxUS)', 'value' => $model->get_options_array());
		}

		/*
		 * return
		 */
		return array(
			array('form_type' => 'select', 'name' => 'netelementtype_id', 'description' => 'NetElement Type', 'value' => $model->html_list(NetElementType::get(['id', 'name']), 'name'), 'hidden' => 0),
			array('form_type' => 'text', 'name' => 'name', 'description' => 'Name'),
			// array('form_type' => 'select', 'name' => 'type', 'description' => 'Type', 'value' => ['NET' => 'NET', 'CMTS' => 'CMTS', 'DATA' => 'DATA', 'CLUSTER' => 'CLUSTER', 'NODE' => 'NODE', 'AMP' => 'AMP']),
			// net is automatically detected in Observer
			// array('form_type' => 'select', 'name' => 'net', 'description' => 'Net', 'value' => $nets),
			array('form_type' => 'ip', 'name' => 'ip', 'description' => 'IP address'),
			array('form_type' => 'text', 'name' => 'link', 'description' => 'HTML Link'),
			array('form_type' => 'select', 'name' => 'prov_device_id', 'description' => 'Provisioning Device', 'value' => $prov_device, 'hidden' => $prov_device_hidden),
			array('form_type' => 'text', 'name' => 'pos', 'description' => 'Geoposition'),
			array('form_type' => 'select', 'name' => 'parent_id', 'description' => 'Parent Object', 'value' => $parents),

			$options_array,
			// array('form_type' => 'select', 'name' => 'state', 'description' => 'State', 'value' => ['OK' => 'OK', 'YELLOW' => 'YELLOW', 'RED' => 'RED'], 'options' => ['readonly']),

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
			['name' => 'Controlling', 'route' => 'NetElement.controlling_edit', 'link' => [$view_var->id, 0, 0]],
			parent::get_form_tabs($view_var)[0]
		];
	}


	/**
	 * Overwrites the base method to handle file uploads
	 */
	public function store($redirect = true)
	{
		// check and handle uploaded KML files
		$this->handle_file_upload('kml_file', storage_path(static::get_model_obj()->kml_path));

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
		$this->handle_file_upload('kml_file', storage_path(static::get_model_obj()->kml_path));

		return parent::update($id);
	}


}
