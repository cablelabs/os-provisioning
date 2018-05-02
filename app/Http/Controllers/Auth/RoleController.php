<?php

namespace App\Http\Controllers\Auth;

use App\Role;
use App\PermissionRole;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;

class RoleController extends BaseController
{
	protected $edit_left_md_size = 5;
	protected $edit_right_md_size = 7;

	public function view_form_fields($model = null)
	{
		return array(
			array('form_type' => 'text', 'name' => 'name', 'description' => 'Name'),
			array('form_type' => 'select', 'name' => 'type', 'description' => 'Type', 'value' => Role::getPossibleEnumValues('type', false)),
			array('form_type' => 'text', 'name' => 'description', 'description' => 'Description'),
		);
	}


	/**
	 * Update right/permission by given core_id
	 *
	 * @param Request $request
	 * @return mixed|string
	 */
	public function update_permission(Request $request)
	{
		try {
			$data = $request->all();
			$rightModel = new PermissionRole();
			$ret = $rightModel->update_permission($data['authmethacore_id'], $data['authmethacore_right'], $data['authmethacore_right_value']);
		} catch (\Exception $e) {
			// @ToDo: Logging the Exception
			//throw $e;
			$ret = $e->getMessage();
		}
		return $ret;
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
}
