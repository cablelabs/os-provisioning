<?php

namespace App;

/*
 * This is a simple placeholder, so that we can use Authmetacore from Eloquent context
 * NOTE: This shit :) is actually used by Command: php artisan nms:auth
 */

class Authmetacore extends BaseModel {

	protected $table = 'authrole_core';

	/**
	 * Get all assigned rights to a role
	 *
	 * @param $role_id
	 * @return array
	 * @throws \Exception
	 */
	public function get_permissions_by_roleid($role_id)
	{
		if (is_null($role_id))
			return [];

		return \DB::table($this->table)
				->join('authcores', 'authcores.id', '=', $this->table . '.core_id')
				->select($this->table . '.*', 'authcores.name', 'authcores.type')
				->where($this->table . '.role_id', '=', $role_id)
				->orderBy('name')
				->get();
	}

	/**
	 * Returns an array of all not assigned permissions to a role
	 *
	 * @param integer $role_id
	 * @return array
	 * @throws \Exception
	 */
	public function get_not_assigned_permissions($role_id)
	{
		$ret = array();

		// get all assigned permissions
		$assigned_permissions = $this->get_permissions_by_roleid($role_id);

		// get all available permissions
		$available_permissions = Authcore::all()->toArray();

		foreach ($available_permissions as $key => $permission) {
			if (!$this->is_assigned($permission, $assigned_permissions)) {
				$ret[] = $permission;
			}
		}

		return $ret;
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

		if ($authmethacore_right_value == 1) {
			$value = 0;
		} elseif ($authmethacore_right_value == 0) {
			$value = 1;
		}

		$ret = \DB::table($this->table)
			->where('id', '=' , $authmethacore_id)
			->update([$authmethacore_right => $value]);

		// check rights - if no right set, delete entry
		$entry = \DB::table($this->table)
			->select('view', 'create', 'edit', 'delete')
			->where('id', '=', $authmethacore_id)
			->get();

		// delete entry if no right set
		if ($entry[0]->view == 0 && $entry[0]->create == 0 && $entry[0]->edit == 0 && $entry[0]->delete == 0) {
			$ret = \DB::table($this->table)
				->where('id', '=', $authmethacore_id)
				->delete();
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

		$data = array(
			'role_id' => $role_id,
			'core_id' => $permission_id,
		);

		foreach ($all_rights as $right) {
			if (in_array($right, $selected_rights)) {
				$data = array_add($data, $right, 1);
			}
		}

		\DB::table($this->table)->insert($data);
	}

	/**
	 * Delete a row by given id
	 *
	 * @param $row_id
	 * @throws \Exception
	 */
	public function delete_permission($row_id)
	{
		\DB::table($this->table)->where('id', '=', $row_id)->delete();
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
		foreach ($assigned_permissions as $assigned_permission) {
			if ($assigned_permission->name == $permission['name'])
				return true;
		}

		return false;
	}
}
