<?php

namespace App\Http\Controllers\Auth;

use Bouncer, Module;
use App\{ Ability, BaseModel, Role, User };
use App\Http\Controllers\BaseViewController;
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

	}

	/**
	 * Update right/permission by given role
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

	public function edit($id)
	{
		$view = parent::edit($id);

		Bouncer::refresh();

		$data = $view->getData();

		$roleAbilities = $data['view_var']->getAbilities()->pluck('title', 'id');
		$roleForbiddenAbilities = $data['view_var']->getForbiddenAbilities()->pluck('title', 'id');
		$modules = Module::collections()->keys();
		$models = collect(BaseModel::get_models());

		// Permissions for the Global Config Pages
		$modelAbilities = collect(['GlobalConfig' => collect([
			'GlobalConfig'	=> $models->pull('GlobalConfig'),
			'BillingBase'	=> $models->pull('BillingBase'),
			'Ccc'			=> $models->pull('Ccc'),
			'HfcBase'		=> $models->pull('HfcBase'),
			'ProvBase'		=> $models->pull('ProvBase'),
			'ProvVoip'		=> $models->pull('ProvVoip'),
		])->keys()]);

		$modelAbilities['Authentication'] = $models->filter(function ($value, $key) {
					return (stripos($value, 'App') !== false);
			})->keys();

		foreach ($modules as $module) {
			$modelAbilities[$module] = $models->filter(function ($value, $key) use ($module) {
					return strpos($value, $module);
			})->keys();
		}

		$modelAbilities = $modelAbilities->reject(function ($value, $key) {
			return $value->isEmpty();
		});

		$userAbilities = Ability::whereNotIn('name', ['*', 'view', 'create', 'update', 'delete'])
							->orWhere('entity_type', '*')
							->get()
							->pluck('title', 'id');

		$userAbilities = $userAbilities->map(function ($title, $id) {
			return collect([
				'title' => BaseViewController::translate_label($title),
				'helperText' => trans('helper.' . $title),
			]);
		});

		return $view->with(compact('id', 'roleAbilities', 'roleForbiddenAbilities', 'modelAbilities', 'userAbilities', 'allowState' ));
	}
}
