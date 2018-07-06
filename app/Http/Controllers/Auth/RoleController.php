<?php

namespace App\Http\Controllers\Auth;

use Bouncer, Module;
use App\{ Ability, BaseModel, Role, User };
use App\PermissionRole;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;

class RoleController extends BaseController
{
	protected $edit_left_md_size = 5;
	protected $edit_right_md_size = 7;
	protected $many_to_many = [
		[
			'field' => 'users_ids',
			'classes' => [User::class, Role::class]
		]
	];

	public function view_form_fields($model = null)
	{
		return array(
			['form_type' => 'text', 'name' => 'name', 'description' => 'Name'],
			['form_type' => 'text', 'name' => 'title', 'description' => 'Title'],
			['form_type' => 'text', 'name' => 'description', 'description' => 'Description'],
			['form_type' => 'text', 'name' => 'rank', 'description' => 'Rank', 'help' => trans('helper.assign_rank')],
			['form_type' => 'select', 'name' => 'users_ids[]', 'description' => 'Assign Users',
				'value' => $model->html_list(User::all(), 'login_name'),
				'options' => [
					'multiple' => 'multiple',
					(Bouncer::can('update', User::class) && Bouncer::can('update', Role::class)) ? '' : 'disabled' => 'true'],
					'help' => trans('helper.assign_users'),
					'selected' => $model->html_list($model->users, 'name')],
		);
	}

	/**
	 * Assign permissions/rights to a role
	 *
	 * @param Request $request
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function assign_permission(Request $request)
	{
		$data = $request->all();

		if (isset($data['permission']))
		{
			foreach ($data['permission'] as $permission_id => $rights) {
				$model = new PermissionRole();
				$ret = $model->assign_permission($data['role_id'], $permission_id, $rights);
			}
		}

		return redirect('admin/Role/' . $data['role_id'] . '/edit');
	}

	/**
	 * Delete permissions/rights from a role
	 *
	 * @param Request $request
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function delete_permission()
	{
		if (\Input::has('delete_ids'))
		{
			foreach (\Input::get('delete_ids') as $row_id => $value) {
				$model = new PermissionRole();
				$ret = $model->delete_permission($row_id);
			}
		}

		return redirect('admin/Role/' . \Input::get('role_id') . '/edit');
	}

	public function edit($id)
	{
		$view = parent::edit($id);
		Bouncer::refresh();
		$data = $view->getData();
		$abilities = $data['view_var']->getAbilities();
		$forbiddenAbilities = $data['view_var']->getForbiddenAbilities();


		return $view->with(compact('id', 'abilities', 'forbiddenAbilities'));
	}
}
