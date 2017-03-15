<?php

namespace App\Http\Controllers;

use App\Authuser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;


class AuthuserController extends BaseController {

	/**
	 * if set to true a create button on index view is available - set to true in BaseController as standard
	 */
    protected $index_create_allowed = true;

    /**
     * defines the formular fields for the edit and create view
     */
	public function view_form_fields($model = null)
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

	/**
	 * Assign roles to user
	 *
	 * @param Request $request
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 * @throws \Exception
	 */
	public function assign_roles(Request $request)
	{
		try {
			$input_data = $request->all();
			$url = route('Authuser.edit', $input_data['user_id']);

			if ($request->isMethod('post')) {
				if (isset($input_data['user_id']) && isset($input_data['role_ids'])) {
					$user = new Authuser();
					foreach ($input_data['role_ids'] as $role_id) {
						$user->assign_roles_for_userid($input_data['user_id'], $role_id);
					}
				}
			}
		} catch (\Exception $e) {
			throw $e;
		}
		return redirect($url);
	}

	/**
	 * Delete selected assigned roles for user
	 *
	 * @param Request $request
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 * @throws \Exception
	 */
	public function delete_assigned_roles(Request $request)
	{
		try {
			$input_data = $request->all();
			$url = route('Authuser.edit', $input_data['user_id']);

			if ($request->isMethod('post')) {
				if (isset($input_data['user_id']) && isset($input_data['role_ids'])) {
					$user = new Authuser();
					foreach ($input_data['role_ids'] as $role_id) {
						$user->delete_roles_by_userid($input_data['user_id'], $role_id);
					}
				}
			}
		} catch (\Exception $e) {
			throw $e;
		}

		return redirect($url);
	}
}