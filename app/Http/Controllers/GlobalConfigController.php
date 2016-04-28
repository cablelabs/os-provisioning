<?php

namespace App\Http\Controllers;

class GlobalConfigController extends BaseController {

	protected $log_level = ['0 - Emergency', '1 - Alert', '2 - Critical', '3 - Error', '4 - Warning', '5 - Notice', '6 - Info', '7 - Debug'];

    /**
     * defines the formular fields for the edit and create view
     */
	public function view_form_fields($model = null)
	{
		// label has to be the same like column in sql table
		return array(
			array('form_type' => 'text', 'name' => 'name', 'description' => 'ISP Name'),
			array('form_type' => 'text', 'name' => 'street', 'description' => 'Street'),
			array('form_type' => 'text', 'name' => 'city', 'description' => 'City'),
			array('form_type' => 'text', 'name' => 'phone', 'description' => 'Phonenumber'),
			array('form_type' => 'text', 'name' => 'mail', 'description' => 'E-Mail Address'),

			array('form_type' => 'select', 'name' => 'log_level', 'description' => 'System Log Level', 'value' => $this->log_level),
			array('form_type' => 'text', 'name' => 'headline1', 'description' => 'Headline 1'),
			array('form_type' => 'text', 'name' => 'headline2', 'description' => 'Headline 2'),
			);
	}

}