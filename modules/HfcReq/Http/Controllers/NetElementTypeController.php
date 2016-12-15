<?php

namespace Modules\HfcReq\Http\Controllers;

class NetElementTypeController extends \BaseController {

    /**
     * defines the formular fields for the edit and create view
     */
	public function view_form_fields($model = null)
	{
		// label has to be the same like column in sql table
		return array(
			array('form_type' => 'text', 'name' => 'name', 'description' => 'Name'),
			// TODO: Make this an upload field for svg icons
			array('form_type' => 'text', 'name' => 'icon_name', 'description' => 'Icon'),
		);
	}


}
