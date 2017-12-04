<?php

namespace App\Http\Controllers;

use App\Authmetacore;
use App\Authrole;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Input;

class AuthroleController extends BaseController
{
	protected $index_create_allowed = true;

	protected $index_delete_allowed = true;

	protected $edit_left_md_size = 6;

	protected $edit_right_md_size = 6;

	public function view_form_fields($model = null)
	{
		return array(
			array('form_type' => 'text', 'name' => 'name', 'description' => 'Name'),
			array('form_type' => 'select', 'name' => 'type', 'description' => 'Type', 'value' => Authrole::getPossibleEnumValues('type', false)),
			array('form_type' => 'text', 'name' => 'description', 'description' => 'Description'),
		);
	}

	/**
	 * Create a new role or restore a soft deleted role if it exists
	 *
	 * @param bool $redirect
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 * @throws \Exception
	 */
	public function store($redirect = true)
	{
		try {
			$data = Input::all();
			$res = Authrole::withTrashed()
				->where('name', $data['name'])
				->restore();

			if ($res == 0) {
				parent::store($redirect);
			}
		} catch (\Exception $e) {
			throw $e;
		}

		return redirect('admin/Authrole');
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
			$rightModel = new Authmetacore();
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
	 * @throws \Exception
	 */
	public function assign_permission(Request $request)
	{
		try {
			$data = $request->all();

			foreach ($data['permission'] as $permission_id => $rights) {
				$model = new Authmetacore();
				$ret = $model->assign_permission($data['role_id'], $permission_id, $rights);
			}
		} catch (\Exception $e) {
			throw $e;
		}

		return redirect('admin/Authrole/' . $data['role_id'] . '/edit');
	}

	/**
	 * Delete permissions/rights from a role
	 *
	 * @param Request $request
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 * @throws \Exception
	 */
	public function delete_permission(Request $request)
	{
		try {
			$data = $request->all();

			foreach ($data['delete_ids'] as $row_id => $value) {
				$model = new Authmetacore();
				$ret = $model->delete_permission($row_id);
			}
		} catch (\Exception $e) {
			throw $e;
		}

		return redirect('admin/Authrole/' . $data['role_id'] . '/edit');
	}
}
