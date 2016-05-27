<?php

namespace Modules\HfcSnmp\Http\Controllers;

class DeviceTypeController extends \BaseController {

    /**
     * defines the formular fields for the edit and create view
     */
	public function view_form_fields($model = null)
	{
		// label has to be the same like column in sql table
		return array(
			array('form_type' => 'text', 'name' => 'name', 'description' => 'Name'),
			array('form_type' => 'text', 'name' => 'vendor', 'description' => 'Vendor'),
			array('form_type' => 'text', 'name' => 'version', 'description' => 'Version'),
			array('form_type' => 'text', 'name' => 'parent_id', 'description' => 'Parent Device Type'),
			array('form_type' => 'textarea', 'name' => 'description', 'description' => 'Description')
		);
	}


}
