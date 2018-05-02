<?php

namespace App;

class Role extends BaseModel
{
	protected static $undeletables = [1 => 'super_admin', 3 => 'director', 4 => 'technician', 5 => 'accounting'];

	public $table = 'roles';

	/*
	 * Relations
	 */
	public function users() {
		return $this->belongsToMany(User::class);
	}

	public function permissions() {
		return $this->belongsToMany(Permission::class);
	}

	/**
	 * Assign permissions to role
	 *
	 * @param Permission $object
	 *
	 */
	public function assignPermissionTo(Permission $permission)
	{
		return $this->permissions->sync($permission);
	}


	/**
	 * Check if user has permissions for module and model
	 *
	 * @param $module
	 * @param $entity
	 * @return bool
	 * @throws \Exception
	 */
	public function has_permissions($module, $entity)
	{
		$ret_val = false;
		$namespace = 'Modules\\' . $module . '\\Entities\\' . $entity;
		if ($module == 'App\\') {
			// separately added page
			if ($entity == 'Config')
				$entity = 'GlobalConfig';
			$namespace = $module . $entity;
		}

		$model_permissions = $this->get_model_permissions();

		if (array_key_exists($namespace, $model_permissions)) {
			$ret_val = true;
		}

		return $ret_val;
	}

	/**
	 * Get a matrix containing user rights for models.
	 *
	 * @author Patrick Reichel
	 *
	 * @return two dimensional array [modelname][rights]
	 */
	public function get_model_permissions() {

		$permissions = array();
		$perm_types = array('create', 'read', 'update', 'delete');

		// get data for each role a user has
		foreach ($this->roles as $role) {
			// get all models for the current role
			$models = $this->_role_models($role['id']);
			// get permissions per model
			foreach ($models as $model) {

				$name = $model->name;

				// create entry without permissions if model not exists
				if (!array_key_exists($name, $permissions)) {
					$perm = array();
					foreach($perm_types as $perm_type) {
						$perm[$perm_type] = 0;
					}
					$permissions[$name] = $perm;
				}

				// use highest rights for the model
				// as a user can hold many roles there can be different permissions for a task ⇒ if one role allows access, than access is granted
				foreach ($perm_types as $perm_type) {
					$permissions[$name][$perm_type] = max($permissions[$name][$perm_type], $model->{$perm_type});
				}

			}

		}

		return $permissions;
	}


	public static function rules($id=null)
	{
		return array(
			'name' => 'required|unique:authrole,name,'.$id.',id,type,role,deleted_at,NULL',
			'type' => 'required|in:role,client'
		);
	}


	public static function view_headline()
	{
		return 'User roles';
	}

	// View Icon
	public static function view_icon()
	{
		return '<i class="fa fa-user-circle text-info"></i>';
	}

	// link title in index view
	public function view_index_label()
	{
		return ['table' => $this->table,
		'index_header' => [$this->table.'.name'],
		'header' => $this->name,
		'order_by' => ['0' => 'desc'],
		'edit' =>['checkbox' => 'set_index_delete'],
	];
	}


	public function set_index_delete()
	{
		if (array_key_exists($this->id, self::$undeletables)) {
				$this->index_delete_disabled = true;
			}
	}

	public function view_has_many()
	{
		$ret['Base']['Permissions']['view']['view'] = 'auth.permissions';
		return $ret;
	}


}
