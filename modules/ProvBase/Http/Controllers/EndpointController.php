<?php

namespace Modules\ProvBase\Http\Controllers;

use Modules\ProvBase\Entities\{Endpoint, Modem};

class EndpointController extends \BaseController {

    /**
     * defines the formular fields for the edit and create view
     */
	public function view_form_fields($model = null)
	{
		// label has to be the same like column in sql table
		return array(
			array('form_type' => 'text', 'name' => 'hostname', 'description' => 'Hostname', 'options' => ['readonly']),
			array('form_type' => 'text', 'name' => 'mac', 'description' => 'MAC Address', 'options' => ['placeholder' => 'AA:BB:CC:DD:EE:FF'], 'help' => trans('helper.mac_formats')),
			array('form_type' => 'textarea', 'name' => 'description', 'description' => 'Description'),

		);
	}

	protected function prepare_input_post_validation($data)
	{
		return unify_mac($data);
	}

}
