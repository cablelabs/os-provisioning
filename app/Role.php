<?php

namespace App;
use Silber\Bouncer\Database\Role as BouncerRole;

class Role extends BouncerRole
{
	public static function rules($id=null)
	{
		return array(
			'name' => 'required|unique:role,name,'.$id.',id,role,deleted_at,NULL',
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
