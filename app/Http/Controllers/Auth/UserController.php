<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\BaseController;

class UserController extends BaseController {

	protected $many_to_many = ['roles_ids'];

    /**
     * Defines the formular fields for the edit and create view.
		 * The labels need to be the same as in the database.
     */
	public function view_form_fields($model = null)
	{
		return [
			['form_type' => 'text', 'name' => 'login_name', 'description' => 'Login'],
			['form_type' => 'password', 'name' => 'password', 'description' => 'Password'],
			['form_type' => 'text', 'name' => 'first_name', 'description' => 'Firstname'],
			['form_type' => 'text', 'name' => 'last_name', 'description' => 'Lastname'],
			['form_type' => 'text', 'name' => 'email', 'description' => 'Email'],
			['form_type' => 'select', 'name' => 'language', 'description' => 'Language', 'value' => User::getPossibleEnumValues('language', false)],
			['form_type' => 'checkbox', 'name' => 'active', 'description' => 'Active', 'value' => '1', 'checked' => true],
			['form_type' => 'select', 'name' => 'roles_ids[]', 'description' => 'Assign Role',
				'value' => $model->html_list(\App\Role::all(), 'name'),
				'options' => ['multiple' => 'multiple'], 'help' => trans('helper.assign_role'),
				'selected' => $model->html_list($model->roles, 'name')]
		];
	}


	public function prepare_input_post_validation ($data)
	{
		$data['password'] = \Hash::make($data['password']);

		return parent::prepare_input($data);
	}

}
