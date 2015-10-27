<?php

use Models\Endpoint;
use Models\Modem;

class EndpointController extends \BaseController {

    /**
     * defines the formular fields for the edit and create view
     */
	public function get_form_fields()
	{
		// label has to be the same like column in sql table
		return array(
			array('form_type' => 'text', 'name' => 'hostname', 'description' => 'Hostname'),
			array('form_type' => 'text', 'name' => 'mac', 'description' => 'MAC address'),
			array('form_type' => 'textarea', 'name' => 'description', 'description' => 'Description'),

		);
	}

}
