<?php

namespace Modules\ProvBase\Http\Controllers;

use Modules\ProvBase\Entities\Endpoint;
use Modules\ProvBase\Entities\Modem;

class EndpointController extends \BaseModuleController {

    /**
     * defines the formular fields for the edit and create view
     */
	public function view_form_fields($model = null)
	{
		// label has to be the same like column in sql table
		return array(
			array('form_type' => 'text', 'name' => 'hostname', 'description' => 'Hostname', 'options' => ['readonly']),
			array('form_type' => 'text', 'name' => 'mac', 'description' => 'MAC address'),
			array('form_type' => 'textarea', 'name' => 'description', 'description' => 'Description'),

		);
	}

}
