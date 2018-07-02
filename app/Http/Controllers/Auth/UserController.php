<?php

namespace App\Http\Controllers\Auth;

use Auth, Bouncer;
use App\{ Role, User };
use App\Http\Controllers\BaseController;

class UserController extends BaseController {

	protected $many_to_many = [
		[
			'field' => 'roles_ids',
			'classes' => [User::class, Role::class]
		]
	];

    /**
     * Defines the formular fields for the edit and create view.
	 * The labels need to be the same as in the database.
     */
	public function view_form_fields($model = null)
	{
		$current_user = Auth::user();
		$current_user_rank = $current_user->getHighestRank();
		$user_model_rank = isset($model) ? $model->getHighestRank() : 0;

		if 	(($current_user_rank < $user_model_rank) ||
			($current_user_rank == $user_model_rank && $current_user != $model))
			abort(403, 'not authorized to edit this user');

		return [
			['form_type' => 'text', 'name' => 'login_name', 'description' => 'Login'],
			['form_type' => 'password', 'name' => 'password', 'description' => 'Password'],
			['form_type' => 'password', 'name' => 'password_confirmation', 'description' => 'Confirm Password'],
			['form_type' => 'text', 'name' => 'first_name', 'description' => 'Firstname'],
			['form_type' => 'text', 'name' => 'last_name', 'description' => 'Lastname'],
			['form_type' => 'text', 'name' => 'email', 'description' => 'Email'],
			['form_type' => 'select', 'name' => 'language', 'description' => 'Language',
				'value' => User::getPossibleEnumValues('language', false)],
			['form_type' => 'checkbox', 'name' => 'active', 'description' => 'Active',
				'value' => '1', 'checked' => true],
			['form_type' => 'select', 'name' => 'roles_ids[]', 'description' => 'Assign Role',
				'value' => $model->html_list(Role::all(), 'name'),
				'options' => [
					'multiple' => 'multiple',
					(Bouncer::can('edit', User::class) && Bouncer::can('edit', Role::class)) ? '' : 'disabled' => 'true'],
				'help' => trans('helper.assign_role'),
				'selected' => $model->html_list($model->roles, 'name')]
		];
	}


	public function prepare_input_post_validation ($data)
	{
		if (isset($data['password']) && empty($data['password']))
			unset($data['password']);
		else
			$data['password'] =\Hash::make($data['password']);

		unset($data['password_confirmation']);

		Bouncer::refresh();

		return parent::prepare_input_post_validation($data);
	}
}
