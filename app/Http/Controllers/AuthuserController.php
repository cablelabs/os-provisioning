<?php

namespace App\Http\Controllers;

use Authuser;

class AuthuserController extends BaseController {

	/**
	 * if set to true a create button on index view is available - set to true in BaseController as standard
	 */
    protected $index_create_allowed = true;

    /**
     * defines the formular fields for the edit and create view
     */
	public function get_form_fields($model = null)
	{

		// label has to be the same like column in sql table
		return array(
			array('form_type' => 'text', 'name' => 'login_name', 'description' => 'Login'),
			array('form_type' => 'password', 'name' => 'password', 'description' => 'Password'),
			array('form_type' => 'text', 'name' => 'first_name', 'description' => 'Firstname'),
			array('form_type' => 'text', 'name' => 'last_name', 'description' => 'Lastname'),
			array('form_type' => 'text', 'name' => 'email', 'description' => 'Email'),
			array('form_type' => 'select', 'name' => 'language', 'description' => 'Language', 'value' => Authuser::getPossibleEnumValues('language', false)),
			array('form_type' => 'checkbox', 'name' => 'active', 'description' => 'Active', 'value' => '1', 'checked' => true),
		);
	}


	public function prepare_input_post_validation ($data)
	{
		$data['password'] = \Hash::make($data['password']);

		return parent::prepare_input($data);
	}

}