<?php

namespace App;

/*
 * This is a simple placeholder, so that we can use Authmetacore from Eloquent context
 * NOTE: This shit :) is actually used by Command: php artisan nms:auth
 */
use Illuminate\Support\Facades\DB;

class Authmetacore extends BaseModel {

	protected $table = 'authmetacore';

	/**
	 * Get all assigned rights to a role
	 *
	 * @param $meta_id
	 * @return array
	 * @throws \Exception
	 */
	public function get_permissions_by_metaid($meta_id)
	{
		$ret = array();

		try {
			if (!is_null($meta_id)) {
				$ret = DB::table($this->table)
					->join('authcores', 'authcores.id', '=', $this->table . '.core_id')
					->select($this->table . '.*', 'authcores.name', 'authcores.type')
					->where($this->table . '.meta_id', '=', $meta_id)
					->get();
			}
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), $e->getCode(), $e);
		}

		return $ret;
	}

	/**
	 * Returns an array of all not assigned permissions to a role
	 *
	 * @param integer $meta_id
	 * @return array
	 * @throws \Exception
	 */
	public function get_not_assigned_permissions($meta_id)
	{
		$ret = array();

		try {
			// get all assigned permissions
			$assigned_permissions = $this->get_permissions_by_metaid($meta_id);

			// get all available permissions
			$available_permissions = Authcore::all()->toArray();

			foreach ($available_permissions as $key => $permission) {
				if (!$this->is_assigned($permission, $assigned_permissions)) {
					$ret[] = $permission;
				}
			}
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), $e->getCode(), $e);
		}
		return $ret;
	}

	public function delete_permissions_by_metaid($permissions, $meta_id)
	{
		try {

		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), $e->getCode(), $e);
		}
	}

	/**
	 * Update view\create\edit\delete permissions for Model
	 *
	 * @param $authmethacore_id
	 * @param $authmethacore_right
	 * @param $authmethacore_right_value
	 * @return mixed
	 * @throws \Exception
	 */
	public function update_permission($authmethacore_id, $authmethacore_right, $authmethacore_right_value)
	{

		try {
			if ($authmethacore_right_value == 1) {
				$value = 0;
			} elseif ($authmethacore_right_value == 0) {
				$value = 1;
			}

			$ret = DB::table($this->table)
				->where('id', '=' , $authmethacore_id)
				->update([$authmethacore_right => $value]);
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), $e->getCode(), $e);
		}
		return $ret;
	}


	/**
	 * Assign permissions to role
	 *
	 * @param $role_id
	 * @param $permission_id
	 * @param $selected_rights
	 * @throws \Exception
	 */
	public function assign_permission($role_id, $permission_id, $selected_rights)
	{
		$all_rights = array('view', 'create', 'edit', 'delete');

		try {
			$data = array(
				'meta_id' => $role_id,
				'core_id' => $permission_id,
			);

			foreach ($all_rights as $right) {
				if (in_array($right, $selected_rights)) {
					$data = array_add($data, $right, 1);
				}
			}

			DB::table($this->table)->insert($data);
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), $e->getCode(), $e);
		}
	}

	/**
	 * Check if a given permission already assigned to a role
	 *
	 * @param array $permission
	 * @param array $assigned_permissions
	 * @return bool
	 * @throws \Exception
	 */
	private function is_assigned ($permission, $assigned_permissions)
	{
		$ret = false;
		try {
			foreach ($assigned_permissions as $assigned_permission) {
				if ($assigned_permission->name == $permission['name']) {
					$ret = true;
				}
			}
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), $e->getCode(), $e);
		}

		return $ret;
	}
}
