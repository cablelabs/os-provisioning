<?php

namespace App;

class Authrole extends BaseModel
{
    public $table = 'authrole';

	protected static $undeletables = array(1 => 'super_admin', 3 => 'director', 4 => 'technician', 5 => 'accounting');

	public static function view_headline()
	{
		return 'User roles';
	}

	public static function rules($id=null)
	{
		return array(
			'name' => 'required|unique:authrole,name,'.$id.',id,type,role,deleted_at,NULL',
			'type' => 'required|in:role,client'
		);
	}


	/*
	 * Relations
	 */
	public function users() {
		return $this->belongsToMany('Authuser', 'authusermeta');
	}

	public function cores() {
		return $this->belongsToMany('Authcore', 'authrole_core');
	}

	/**
	 * returns all cores matching the given meta ID and core type
	 *
	 * @param $role_id ID of the meta entry to fetch data for
	 * @param $core_type [model|net]
	 *
	 * @return database data
	 */
	public static function cores_by_role($role_id, $core_type) {
		return \DB::table('authrole_core')
					->join('authcores', 'authrole_core.core_id', '=', 'authcores.id')
					->select('authcores.name', 'authrole_core.view', 'authrole_core.create', 'authrole_core.edit', 'authrole_core.delete')
					->where('authrole_core.role_id', '=', $role_id)
					->where('authcores.type', 'LIKE', $core_type)
					->get();
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
