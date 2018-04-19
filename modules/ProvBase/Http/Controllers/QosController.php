<?php

namespace Modules\ProvBase\Http\Controllers;

use Modules\ProvBase\Entities\Qos;

class QosController extends \BaseController {

	/**
	 * defines the formular fields for the edit and create view
	 */
	public function view_form_fields($model = null)
	{
		// label has to be the same like column in sql table
		return array(
			array('form_type' => 'text', 'name' => 'name', 'description' => 'Name'),
			array('form_type' => 'text', 'name' => 'ds_rate_max', 'description' => 'DS Rate [MBit/s]'),
			array('form_type' => 'text', 'name' => 'us_rate_max', 'description' => 'US Rate [MBit/s]'),
		);
	}

	public function prepare_input($data)
	{
		$data['ds_rate_max_help'] = $data['ds_rate_max_help'] ? : $data['ds_rate_max'] * 1024 * 1024;
		$data['us_rate_max_help'] = $data['ds_rate_max_help'] ? : $data['us_rate_max'] * 1024 * 1024;

		return parent::prepare_input($data);
	}
}
