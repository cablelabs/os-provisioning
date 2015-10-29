<?php

use Models\Device;

class DeviceController extends \BaseController {

    /**
     * defines the formular fields for the edit and create view
     */
	public function get_form_fields($model = null)
	{
		if (!$model)
			$model = new Device;

		// label has to be the same like column in sql table
		return array(
			array('form_type' => 'select', 'name' => 'devicetype_id', 'description' => 'Name', 'value' => $model->html_list($model->devicetypes(), 'name')),
			array('form_type' => 'text', 'name' => 'name', 'description' => 'Name'),
			array('form_type' => 'text', 'name' => 'ip', 'description' => 'IP address'),
			array('form_type' => 'text', 'name' => 'community_ro', 'description' => 'Community RO'),
			array('form_type' => 'text', 'name' => 'community_rw', 'description' => 'Community RW'),
			array('form_type' => 'text', 'name' => 'address1', 'description' => 'Address Line 1'),
			array('form_type' => 'text', 'name' => 'address2', 'description' => 'Address Line 2'),
			array('form_type' => 'text', 'name' => 'address3', 'description' => 'Address Line 3'),
			array('form_type' => 'textarea', 'name' => 'description', 'description' => 'Description')
		);
	}


}
