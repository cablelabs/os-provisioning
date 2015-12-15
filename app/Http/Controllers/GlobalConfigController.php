<?php 

namespace App\Http\Controllers;

class GlobalConfigController extends BaseController {

    /**
     * defines the formular fields for the edit and create view
     */
	public function get_form_fields($model = null)
	{
		// label has to be the same like column in sql table
		return array(
			array('form_type' => 'text', 'name' => 'name', 'description' => 'ISP Name'),
			array('form_type' => 'text', 'name' => 'street', 'description' => 'Street'),
			array('form_type' => 'text', 'name' => 'city', 'description' => 'City'),
			array('form_type' => 'text', 'name' => 'phone', 'description' => 'Phonenumber'),
			array('form_type' => 'text', 'name' => 'mail', 'description' => 'E-Mail Address'),

			array('form_type' => 'text', 'name' => 'log_level', 'description' => 'System Log Level'),
			array('form_type' => 'text', 'name' => 'headline1', 'description' => 'Headline 1'),
			array('form_type' => 'text', 'name' => 'headline2', 'description' => 'Headline 2'),

			);
	}
	
}