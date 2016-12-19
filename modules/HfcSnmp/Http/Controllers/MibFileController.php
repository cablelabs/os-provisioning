<?php

namespace Modules\HfcSnmp\Http\Controllers;

use Modules\HfcSnmp\Entities\MibFile;

class MibFileController extends \BaseController {

    /**
     * defines the formular fields for the edit and create view
     */
	public function view_form_fields($model = null)
	{
		if (!$model)
			$model = new MibFile;

		// label has to be the same like column in sql table
		return array(
			// array('form_type' => 'select', 'name' => 'devicetype_id', 'description' => 'Device Type', 'value' => $model->html_list($model->devicetypes(), 'name')),

			array('form_type' => 'text', 'name' => 'name', 'description' => 'Name'),
			array('form_type' => 'text', 'name' => 'filename', 'description' => 'Filename'),
			array('form_type' => 'text', 'name' => 'version', 'description' => 'Version'),
			array('form_type' => 'textarea', 'name' => 'description', 'description' => 'Description'),
		);
	}

}
